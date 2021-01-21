<div id="form_pesq_cadastro" style="display: block;">
    <div class="form-row" id="input_nom pessoa">
        <div class="form-group col-md-12">
            <label class="col-form-label negrito" for="input_nom_pessoa">Pessoa</label>
            <input id="pessoa_busca" type="text" class="form-control" name="pessoa_busca" autofocus >
            <input type="text" id='GAB_PESSOA_cod_pessoa' name="GAB_PESSOA_cod_pessoa" hidden="true"  readonly>
            <img src="" alt="Imagem de Municipe" id="img_pessoa" name="img_pessoa" style="max-widht: 150px; max-height: 150px;" hidden="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label class="col-form-label negrito" for="input_nom_pessoa">Data</label>
            <input id="dat_atendimento" type="date" name="dat_atendimento" class="form-control" autofocus > 
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label class="col-form-label negrito" for="input_tipo_atendimento">Tipo de Atendimento</label>
            <select  class="form-control" id="GAB_TIPO_ATENDIMENTO_cod_tipo">
                @foreach ($tipoAtendimento as $tipoAtendimento)
                    <option  value="{{ $tipoAtendimento->cod_tipo}}">{{ $tipoAtendimento->nom_tipo}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">  
            <label class="col-form-label negrito" for="input_status_atendimento">Situação do Atendimento</label>
            <select  class="form-control" id="GAB_STATUS_ATENDIMENTO_cod_status">
                @foreach ($statusAtendimento as $statusAtendimento)
                    <option  value="{{$statusAtendimento->cod_status}}">{{$statusAtendimento->nom_status}}</option>
                    @endforeach
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label>Preencha todos campos para realizar uma busca.</label><!--Mudar-->
        </div>
    </div>
    <a id="Cadastrar" class="btn btn-primary" >Cadastrar</a>
    <a id="Pesquisar" class="btn btn-primary" >Pesquisar</a>
</div>

    <div class=form-group id="form_pesq_result" style="display:none;" >
    <label>Clique sobre um atendimento para selecionar</label>
        <table id="tb_saida_pesquisa" class="mtab table table-hover table-responsive" cellspacing="10" width="100%">
        <thead>
            <tr>
                <th>Data</th>
                <th>Pessoa</th>
                <th>Status</th>
                <th>Tipo</th>
            </tr>
            <tbody id="resultado_pesquisa"></tbody>
        </table>    
    </div>

    <div class=form-group id="form_cad_result" style="display:none;">
        <h5 class="negrito">Atendimento Relacionado</h5>
        <label style="font-weight: bolder">Data:</label> <label id="data"></label>
        <br>
        <label style="font-weight: bolder">Pessoa:</label> <label id="pessoa"></label>
        <br>
        <label style="font-weight: bolder">Doc.Identificação:</label> <label id="ident"></label>
        <br>
        <label style="font-weight: bolder">Tipo:</label> <label id="tipo"></label>
        <br>
        <label style="font-weight: bolder">Situação:</label> <label id="situacao"></label>
        <br>
        <input type="hidden" name="GAB_ATENDIMENTO_cod_atendimento" id="GAB_ATENDIMENTO_cod_atendimento"> <!--Passagem para o banco de dados-->
        <a id="Alterar" class="btn btn-primary"> Alterar Atendimento</a> 
    </div>
    
<script type="text/javascript" >
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function(){
        $( "#pessoa_busca" ).autocomplete({
            source: function( request, response ) {
            // Fetch data
                $.ajax({
                        url:"{{route('atendimento.seleciona_pessoa')}}", //rota da requisição
                        type: 'post',                                    //método       
                        dataType: "json",                                //tipo de dado
                        data: {                                          //tipo dado enviado         
                        _token: CSRF_TOKEN,                              //token nescessario laravel
                        search: request.term                             //termo de busca
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#pessoa_busca').val(ui.item.nome); // display the selected text //mostra o texto selecionado

                $('#GAB_PESSOA_cod_pessoa').val(ui.item.value); // save selected id to input //salva o id do input
                    
                if (ui.item.path_imagem!=null){
                    $('#img_pessoa').attr("src","../../storage/"+ui.item.path_imagem);
                    $('#img_pessoa').attr("hidden",false);
                }else{
                    $('#img_pessoa').attr("src","../../utils/sem-imagem.jpg");
                    $('#img_pessoa').attr("hidden",false);
                }
                return false;
            }
        });
        $("#pessoa_busca").on('keyup', function(e) {    //quando uma tecla é apertada
            if ($('#pessoa_busca').is(':empty')){       //verifica se esta vazio
                $('#img_pessoa').attr("hidden",true);  //Caso esteja limpa os campos de código e a imagem                            
                $('#GAB_PESSOA_cod_pessoa').val("");
            }
        });
    });
</script>
<script type="text/javascript" >
    function enviaAtendimento(id,data,pessoa,tipo,situacao){
        // Formatando data para o padrão brasileiro
        //var split = data.split('/');
        //data_formatada = split[2] + "/" + split[1] + "/" + split[0];
        //Enviando dados para a div de saida
        $('#data').text(data);
        $('#pessoa').text(pessoa);
        $('#tipo').text(tipo);
        $('#situacao').text(situacao);
        $("#GAB_ATENDIMENTO_cod_atendimento").val(id);
        $("#form_cad_result").css("display", "block"); 
        $("#form_pesq_cadastro").css("display", "none");  
        $("#form_pesq_result").css("display", "none");                                
    }        
    $(function(){
        $('#Alterar').on("click",function(event){
            $("#form_cad_result").css("display", "none");
            $("#form_pesq_cadastro").css("display", "block");  
            $("#form_pesq_result").css("display", "none");     
        });
        $('#Cadastrar').on("click",function(event){
            $.ajax({ //enviar requisição ajax
                url:"{{url('/documento/cadAtendimento')}}", 
                type: "post",
                dataType:'json',
                data: {
                    _token: CSRF_TOKEN,
                    GAB_TIPO_ATENDIMENTO_cod_tipo: jQuery('#GAB_TIPO_ATENDIMENTO_cod_tipo option:selected').val(),
                    dat_atendimento: jQuery('#dat_atendimento').val(),
                    GAB_PESSOA_cod_pessoa: jQuery('#GAB_PESSOA_cod_pessoa').val(),
                    GAB_STATUS_ATENDIMENTO_cod_status: jQuery('#GAB_STATUS_ATENDIMENTO_cod_status option:selected').val(),     
                },
                success:function(result){ //retorno do controler com os dados cadastrados
                    split = result.data.split('-');
                    novadata = split[2] + "/" +split[1]+"/"+split[0]; 
                    $('#data').text(result.novadata);
                    $('#pessoa').text(result.pessoa);
                    $('#ident').text(result.ident);
                    $('#tipo').text(result.tipo);
                    $('#situacao').text(result.situacao);
                    $("#GAB_ATENDIMENTO_cod_atendimento").val(result.codigo);
                    $("#form_cad_result").css("display", "block");
                    $("#form_pesq_cadastro").css("display", "none");
                    $("#form_pesq_result").css("display", "none");
                }
            });
        });
        $('#Pesquisar').on("click",function(event){
            $.ajax({ //enviar requisição ajax
                url:"{{url('/documento/pesqAtendimento')}}", 
                type: "post",
                dataType:'json',
                data: {
                    _token: CSRF_TOKEN, //token de validação do laravel
                    GAB_TIPO_ATENDIMENTO_cod_tipo: jQuery('#GAB_TIPO_ATENDIMENTO_cod_tipo option:selected').val(),
                    dat_atendimento: jQuery('#dat_atendimento').val(),
                    GAB_PESSOA_cod_pessoa: jQuery('#GAB_PESSOA_cod_pessoa').val(),
                    GAB_STATUS_ATENDIMENTO_cod_status: jQuery('#GAB_STATUS_ATENDIMENTO_cod_status option:selected').val(),     
                },
                success:function(result){
                    for(i=0;i<5;i++){
                        var data=result[i].dat_atendimento;
                        splitAux = data.split('T');
                        split = splitAux[0].split('-');
                        novadata = split[2] + "/" +split[1]+"/"+split[0]; 
                        result[i].dat_atendimento = novadata;
                    }
                    let tabela = ``; // declara a variável vazia
                    // vai montando as linhas com os valores do JSON
                    for(let item of result){
                        tabela += `<tr onclick="enviaAtendimento(${item.cod_atendimento},'${item.dat_atendimento}','${item.GAB_PESSOA_cod_pessoa}','${item.GAB_STATUS_ATENDIMENTO_cod_status}','${item.GAB_TIPO_ATENDIMENTO_cod_tipo}');">
                                        <td>${item.dat_atendimento}</td>
                                        <td>${item.GAB_PESSOA_cod_pessoa}</td>
                                        <td>${item.GAB_STATUS_ATENDIMENTO_cod_status}</td>
                                        <td>${item.GAB_TIPO_ATENDIMENTO_cod_tipo}</td>
                                    </tr>`;
                    } 
                    $('#resultado_pesquisa').html(tabela); // insere tudo no tbody*/
                    $("#form_pesq_result").css("display", "block");                                 
                }
            });
        });

    }); 
</script>