<style type="text/css">
        h3 {text-align: center; margin: 0px}
        h4 {text-align: center; margin: 0px}
        p {text-align: center; margin: 0px}
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td{
            border: 1px solid;
            border-color: #1C1C1C;
            text-align: center;
            padding:5px;
        }
        th {
            text-align: center;
            border: 1px solid;
            border-color: #1C1C1C;
        }
        .sem_borda{
          border-right:0px;
	      border-left:0px;
          height:1px;
        }
        @page {
            footer: page-footer;
        }
</style>

    <h3>{{$agentePolitico->cargoPolitico->nom_car_pol}} {{$agentePolitico->nom_vereador}}</h3>
    <h4>{{$agentePolitico->nom_orgao}}</h4>
    <p>
        {{$agentePolitico->nom_endereco}}, {{$agentePolitico->nom_numero}} - {{$agentePolitico->nom_complemento}} - {{$agentePolitico->nom_cidade}}/{{$agentePolitico->nom_estado}} - CEP:{{$agentePolitico->num_cep}}
    </p>
    <h3 style="margin:10px;text-decoration: underline">Gestão de Gabinete - Relatório de Documentos</h3>
    <table class="tabela">
        <tr>
            <th>Data</th>
            <th>Número/Ano</th>
            <th>Tipo</th>
            <th>Situação</th>
            <th>Unidade</th>
            <th>Atendimento</th>
            <th>Resposta</th>
        </tr>
        @php $i=0; @endphp
        @foreach($documentos as $documento)
        @php $i++; @endphp
        <tr>
            <td width="8%">
                {{date('d/m/Y', strtotime($documento->dat_documento))}}
            </td>
            <td width="10%">
                {{$documento->nom_documento}}/{{$documento->dat_ano}}
            </td>
            <td width="10%">
                {{$documento->tipoDocumento->nom_tip_doc}}
            </td>
            <td width="10%">
                {{$documento->situacaoDoc->nom_status}}
            </td>
            <td width="10%">
                {{$documento->unidadeDocumento->nom_uni_doc}}
            </td>
            <td width="20%" style="text-align: left;">
                @if($documento->GAB_ATENDIMENTO_cod_atendimento!=null)
                    @if($documento->antedimentoRelacionado->ind_status=="A")
                        {{date('d/m/Y', strtotime($documento->antedimentoRelacionado->dat_atendimento))}}
                        <br>
                        {{$documento->antedimentoRelacionado->pessoa->nom_nome}}
                        <br>
                        @if($documento->antedimentoRelacionado->pessoa->ind_pessoa=="PF")
                            CPF:
                        @else
                            CNPJ:    
                        @endif
                        {{$documento->antedimentoRelacionado->pessoa->cod_cpf_cnpj}}
                        <br>
                        Tipo: {{$documento->tipoDocumento->nom_tip_doc}}
                        <br>
                        Situação: {{$documento->situacaoDoc->nom_status}}
                    @endif
                @endif
            </td>
            <td width="10%" style="text-align: left;">
                @if($documento->dat_resposta!=null)
                    {{date('d/m/Y', strtotime($documento->dat_resposta))}}
                @endif
            </td>
        </tr>
        
        @endforeach
    </table>
    <label>Total de Registros:{{$i}} o PDF imprime até 500 registros.</label>
    <htmlpagefooter name="page-footer">
        {PAGENO}
    </htmlpagefooter>