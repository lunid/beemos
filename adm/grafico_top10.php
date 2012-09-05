<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//

    session_start();
    
    require_once 'class/usuario.php';
    require_once '../class/util.php';
    
    if(isset($_SESSION['ADM_USUARIO'])){
        //Valida permissão de acesso ao Usuário
        $ret = Usuario::validaAcesso(unserialize($_SESSION['ADM_USUARIO']));
        
        if(!$ret->status){
            //redirecionando para efetuar login
            header("Location: index.php");  
        }
    }else{
        //redirecionando para efetuar login
        header("Location: index.php");
    }
    
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/adm/class/Top10log.php";
    
    $top10 = new Top10log();
    
    if($_POST){
        $data_inicio    = Util::formataData($_POST['data_inicio'], "AAAA-MM-DD");
        $data_final     = Util::formataData($_POST['data_final'], "AAAA-MM-DD");
    }else{
        $data_inicio    = date("Y-m-d", mktime(0, 0, 0, date("m"), (date("d")-30), date("Y")));
        $data_final     = date("Y-m-d");
    }
    
    $rs = $top10->relatorioTop10($data_inicio, $data_final);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | TOP 10 | Gráfico</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="../js/libs/mask/mask.js"></script>
        <script type="text/javascript" src="../js/libs/charts/highcharts.js"></script>
        
        <? if($rs->status){ ?>
        <script type="text/javascript">
            $(function () {
                var chart;
                $(document).ready(function() {
                    chart = new Highcharts.Chart({
                        chart: {
                            renderTo: 'container',
                            type: 'column',
                            height: '600'
                        },
                        title: {
                            text: 'Histórico de TOP 10'
                        },
                        xAxis: {
                            categories: ['TOP 10']
                        },
                        yAxis: {
                            min: 0,
                            labels: {
                                enabled: false
                            },
                            title: {
                                text: ''
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                },
                                formatter: function() {
                                    return this.stack;
                                }
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true,
                                    color: '#000',
                                    formatter: function() {
                                            return this.series.name;
                                    }
                                }
                            }
                        },
                        series: [
                            <? foreach($rs->data as $row){ ?>
                            {
                                name: 'Pos 1 - <?=$row->getPos1();?>',
                                data: [20],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos1()]?>'
                            },
                            {
                                name: 'Pos 2 - <?=$row->getPos2();?>',
                                data: [18],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos2()]?>'
                            },
                            {
                                name: 'Pos 3 - <?=$row->getPos3();?>',
                                data: [17],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos3()]?>'
                            },
                            {
                                name: 'Pos 4 - <?=$row->getPos4();?>',
                                data: [16],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos4()]?>'
                            },
                            {
                                name: 'Pos 5 - <?=$row->getPos5();?>',
                                data: [15],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos5()]?>'
                            },
                            {
                                name: 'Pos 6 - <?=$row->getPos6();?>',
                                data: [14],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos6()]?>'
                            },
                            {
                                name: 'Pos 7 - <?=$row->getPos7();?>',
                                data: [13],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos7()]?>'
                            },
                            {
                                name: 'Pos 8 - <?=$row->getPos8();?>',
                                data: [12],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos8()]?>'
                            },
                            {
                                name: 'Pos 9 - <?=$row->getPos9();?>',
                                data: [11],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos9()]?>'
                            },
                            {
                                name: 'Pos 10 - <?=$row->getPos10();?>',
                                data: [10],
                                stack: '<?=Util::formataData($row->getDataLog());?>',
                                color: '#<?=$rs->colors[$row->getPos10()]?>'
                            },
                            <? } ?>
                        ]
                    });
                });
            });
        </script>
        <? } ?>
    </head>
    <body>
        <div>
            <form action="grafico_top10.php" method="POST">
                <table>
                    <tr>
                        <td>
                            Data de início: 
                        </td>
                        <td>
                            <input type="text" name="data_inicio" id="data_inicio" value="<?=Util::formataData($data_inicio)?>" />
                        </td>

                        <td>
                            Data: 
                        </td>
                        <td>
                            <input type="text" name="data_final" id="data_final" value="<?=Util::formataData($data_final)?>" />
                        </td>
                        <td>
                            <input type="submit" value="Filtrar"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="container" style="min-width:400px;height:400px; margin:0 auto">
            <? 
                if(!$rs->status){ 
                    echo $rs->msg;
                }
            ?>
        </div>
        <script type="text/javascript">
            $("#data_inicio").mask("99/99/9999");
            $("#data_final").mask("99/99/9999");
        </script>
    </body>
</html>
