    // Lê parâmetros da URL
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');

    if (erro === 'senha') {
      document.getElementById('mensagem').innerText = 'Senha incorreta.';
    } else if (erro === 'usuario') {
      document.getElementById('mensagem').innerText = 'Usuário não encontrado.';
    } else if (erro === 'preencha') {
      document.getElementById('mensagem').innerText = 'Preencha todos os campos.';
    }