function logout(){
    if (confirm('Deseja realmente encerrar a sessão atual?')) {
        window.location.href='/interbits/auth/logout/';
    }
}
