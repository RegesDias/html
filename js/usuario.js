//###############################Funções###########################################
function getUsuarioCodigo() {
    $('#carregando').show();
    $.ajax({
        url: 'acoes/usuario/listarCodigo.php',
        method: 'GET',
        dataType: 'json'
    }).done(function(result){
        var size = result.length+1;
        $("#listaUsuario").empty();
        $("#listaUsuarioNome").empty();
        $('#listaUsuario').attr("size", size);
        $('#listaUsuarioNome').attr("size", size);
        msn('success','Servidores ordenados por codigo!');
        preenchimentoSelect(result);
    }).fail(function() {
       $(location).attr('href', 'index.html');
    }).always(function() {
        $('#carregando').hide();
    });
};
function getUsuarioNome() {
    $('#carregando').show();
    $.ajax({
        url: 'acoes/usuario/listarNome.php',
        method: 'GET',
        dataType: 'json'
    }).done(function(result){
        var size = result.length+1;
        $("#listaUsuario").empty();
        $("#listaUsuarioNome").empty();
        $('#listaUsuario').attr("size", size);
        $('#listaUsuarioNome').attr("size", size);
        msn('success','Servidores ordenados por nome!');
        preenchimentoSelect(result);
    }).fail(function() {
        $(location).attr('href', 'index.html');
    }).always(function() {
        $('#carregando').hide();
    });
};
function getUsuarioCpfNome(dado){
    $('#carregando').show();
    $.ajax({
        url: 'acoes/usuario/buscaCpfNome.php?dado='+dado,
        method: 'GET',
        dataType: 'json'
    }).done(function(result){
        var total = result.length+1;
        var size = total-1;
        if (total>0){
            msn('success','Total de '+total+' encontrado(s)!');
            $("#listaUsuario").empty();
            $("#listaUsuarioNome").empty();
            $('#listaUsuario').attr("size", size);
            $('#listaUsuarioNome').attr("size", size);
            preenchimentoSelect(result);
        }else{
            msn('error','Nenhum servidor encontrado!');
        }
    }).fail(function() {
        $(location).attr('href', 'index.html');
    }).always(function() {
        $('#carregando').hide();
    });
};
function getUsuarioDados(codfunc){
    $('#carregando').show();
    $.ajax({
        url: 'acoes/usuario/buscarCpf.php?codfunc='+codfunc,
        method: 'GET',
        dataType: 'json'
    }).done(function(dadosUsuario){
        console.log(dadosUsuario);
        $('#UsuarioNome').val(dadosUsuario[0].nome);
        $('#UsuarioCpfs').val(dadosUsuario[0].CPF);
        $('#UsuarioStatus').val(dadosUsuario[0].status);
        $('#UsuarioDataCriacao').val(converteDataHoraBr(dadosUsuario[0].dataHora));
        $('#consultaPessoalheckbox').prop("checked", dadosUsuario[0].atendimentoEntrada);
        $('#atendimentoEntradaCheckbox').prop("checked", dadosUsuario[0].atendimentoEntrada);
        $('#atendimentoAgendaCheckbox').prop("checked", dadosUsuario[0].atendimentoAgenda);
        $('#alterarSenhaCheckbox').prop("checked", dadosUsuario[0].alterarSenha);
        $('#usuariosCheckbox').prop("checked", dadosUsuario[0].usuarios);
        $('#modal-Usuario').modal('show');
        $('#chaveLabel').html(dadosUsuario[0].email);
        $('#dataCriacaoLabel').html(converteDataHoraBr(dadosUsuario[0].dataHora));
        $('#dataUltimoLoginLabel').html(converteDataHoraBr(dadosUsuario[0].ultimoLogin));
        
    }).fail(function() {
        $(location).attr('href', 'index.html');
    }).always(function() {
        $('#carregando').hide();
    });
};
function preenchimentoSelect(result){
    for (var i = 0; i < result.length; i++) {
        $('#listaUsuario').prepend('<option value='+ result[i].CPF +'> '+result[i].CPF+'</option>');
        $('#listaUsuarioNome').prepend('<option value='+ result[i].CPF +'> '+result[i].nome+'</option>');
    }
};

//###############################Ações###########################################

$("#visualizarServidor").on("click", function() {
    var codfunc =  $('#listaUsuario option:selected').val();
    getUsuarioDados(codfunc);
});

$('#btnMatriculaCpfNome').on("click", function(){
    var textMatriculaCpfNome = $('#textMatriculaCpfNome').val();
    getUsuarioCpfNome(textMatriculaCpfNome);
    $('#textMatriculaCpfNome').val('');
    $('#visualizarServidor').attr("disabled","disabled");
});
$('#textMatriculaCpfNome').keyup(function(){
    $('#btnMatriculaCpfNome').removeAttr('disabled');
    $('#visualizarServidor').attr("disabled","disabled");
});
$('#optionUsuarioCodigo').on("click", function(){
    getUsuarioCodigo();
    $('#visualizarServidor').attr("disabled","disabled");
    $('#btnMatriculaCpfNome').attr("disabled","disabled");
    $('#textMatriculaCpfNome').val('');
});
$('#optionUsuarioNome').on("click", function(){
    getUsuarioNome();
    $('#visualizarServidor').attr("disabled","disabled");
    $('#btnMatriculaCpfNome').attr("disabled","disabled");
    $('#textMatriculaCpfNome').val('');
});
$('#listaUsuario').change(function(){
    $('#visualizarServidor').removeAttr('disabled');
    $('#btnMatriculaCpfNome').attr("disabled","disabled");
    $('#textMatriculaCpfNome').val('');
});
$('#listaUsuarioNome').change(function(){
    $('#visualizarServidor').removeAttr('disabled');
    $('#btnMatriculaCpfNome').attr("disabled","disabled");
    $('#textMatriculaCpfNome').val('');
});

$(document).ready(function(){
    data = new Date();
    $('#dataAgenda').val(converteDataUS(data));
    getUsuarioCodigo();
});