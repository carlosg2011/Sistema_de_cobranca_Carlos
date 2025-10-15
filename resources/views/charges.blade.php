<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Minhas Cobranças</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa; /* tom suave pra fundo */
      font-family: 'Inter', sans-serif;
    }
    .card-summary {
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .status-pending { color: #ffc107; font-weight: 600; }
    .status-paid { color: #28a745; font-weight: 600; }
    .status-canceled { color: #dc3545; font-weight: 600; }
    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.05);
      padding: 1rem;
    }
    .modal-content {
      border-radius: 12px;
    }
    .btn-new {
      background-color: #007bff;
      color: white;
    }
    .btn-new:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <!-- Cabeçalho / título -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Painel de Cobranças</h1>
      <button class="btn btn-outline-danger" onclick="logout()">Logout</button>
    </div>

    <!-- Resumo (cards) -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card card-summary text-center p-3">
          <div class="card-body">
            <h5 class="card-title">Saldo em Conta</h5>
            <p class="display-6" id="accountBalance">R$ 0,00</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-summary text-center p-3">
          <div class="card-body">
            <h5 class="card-title">Cobranças Pendentes</h5>
            <p class="display-6" id="pendentesCount">0</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-summary text-center p-3">
          <div class="card-body">
            <h5 class="card-title">Cobranças Pagas</h5>
            <p class="display-6" id="pagasCount">0</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Botões principais -->
    <div class="mb-3 text-end">
      <button class="btn btn-primary me-2" onclick="criarCobranca()">Nova Cobrança</button>
    </div>

    <!-- Tabela de cobranças -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Valor</th>
              <th>Vencimento</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="chargesTable">
            <tr><td colspan="5" class="text-center">Carregando cobranças...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal de criação (Bootstrap) -->
  <div class="modal fade" id="modalCobranca" tabindex="-1" aria-labelledby="modalCobrancaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modalCobrancaLabel" class="modal-title">Nova Cobrança</h5>
          <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="valor" class="form-label">Valor (R$)</label>
            <input type="text" class="form-control" id="valor" placeholder="1234,56">
          </div>
          <div class="mb-3">
            <label for="vencimento" class="form-label">Data de vencimento</label>
            <input type="date" class="form-control" id="vencimento">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
          <button type="button" class="btn btn-success" onclick="enviarCobranca()">Criar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- scripts (jQuery, Bootstrap, IMask...) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/imask"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script>
        const token = localStorage.getItem("auth_token");
        const apiUrl = "http://localhost:8000/api";

        if (!token) {
            window.location.href = "/login";
        }

        function logout() {
            $.ajax({
                url: apiUrl + "/logout",
                type: "POST",
                headers: {
                    Authorization: "Bearer " + token
                },
                success: function() {
                    localStorage.removeItem("auth_token");
                    window.location.href = "/login";
                }
            });
        }

        function atualizarContadores(charges) {
      let pendentes = 0, pagas = 0;
      charges.forEach(c => {
        if (c.status === 'pending') pendentes++;
        if (c.status === 'paid') pagas++;
      });
      $('#pendentesCount').text(pendentes);
      $('#pagasCount').text(pagas);
    }

        function formatarDataISO(dataISO) {
            const data = new Date(dataISO);
            const dia = String(data.getDate()).padStart(2, '0');
            const mes = String(data.getMonth() + 1).padStart(2, '0');
            const ano = data.getFullYear();
            return `${dia}/${mes}/${ano}`;
        }

       function carregarCobrancas() {
      $.ajax({
        url: apiUrl + "/charges",
        method: "GET",
        headers: {
          Authorization: "Bearer " + token
        },
        success: function(charges) {
          const tbody = $("#chargesTable");
          tbody.empty();

          atualizarContadores(charges);

          if (charges.length === 0) {
            tbody.append("<tr><td colspan='5' class='text-center'>Nenhuma cobrança encontrada.</td></tr>");
            return;
          }

          charges.forEach(charge => {
            const statusClass = "status-" + charge.status;
            const btns = charge.status === 'pending' ?
              `<button class="btn btn-sm btn-success me-1" onclick="alterarStatus(${charge.id}, 'paid')">Pagar</button>
               <button class="btn btn-sm btn-danger" onclick="alterarStatus(${charge.id}, 'canceled')">Cancelar</button>` : "-";

            tbody.append(`
              <tr>
                <td>${charge.id}</td>
                <td>R$ ${parseFloat(charge.amount).toFixed(2)}</td>
                <td>${formatarDataISO(charge.due_date)}</td>
                <td class="${statusClass}">${charge.status.charAt(0).toUpperCase() + charge.status.slice(1)}</td>
                <td>${btns}</td>
              </tr>
            `);
          });
        },
        error: function(xhr) {
          console.error("Erro ao carregar cobranças:", xhr.responseText);
        }
      });
    }

        function criarCobranca() {
           const modal = new bootstrap.Modal(document.getElementById('modalCobranca'));
    modal.show();

        }

        function fecharModal() {
            const modalElement = document.getElementById('modalCobranca');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }

    // Limpar os campos
    document.getElementById('valor').value = '';
    document.getElementById('vencimento').value = '';
        }

        function enviarCobranca() {
            const valor = parseFloat(document.getElementById('valor').value);
            const data = document.getElementById('vencimento').value;

            if (!valor || valor <= 0 || !data) {
                alert("Preencha todos os campos corretamente.");
                return;
            }

            $.ajax({
                url: apiUrl + "/charges",
                method: "POST",
                headers: {
                    Authorization: "Bearer " + token,
                    "Content-Type": "application/json"
                },
                data: JSON.stringify({
                    amount: valor,
                    due_date: data
                }),
                success: function() {
                    fecharModal();
                    alert("Cobrança criada com sucesso!");
                    carregarCobrancas();
                },
                error: function(xhr) {
                    alert("Erro ao criar cobrança: " + (xhr.responseJSON?.message || "verifique os campos"));
                }
            });
        }

        function alterarStatus(id, status) {
            $.ajax({
                url: apiUrl + "/charges/" + id,
                method: "PATCH",
                headers: {
                    Authorization: "Bearer " + token,
                    "Content-Type": "application/json"
                },
                data: JSON.stringify({
                    status: status
                }),
                success: function() {
                    alert("Status alterado com sucesso!");
                    carregarCobrancas();
                },
                error: function(xhr) {
                    alert("Erro ao alterar status: " + (xhr.responseJSON?.message || "verifique os campos"));
                }
            });
        }

        carregarCobrancas();
    </script>
</body>

</html>