<?php
include "valida_secao.inc";

$id = $_SESSION["id"];
include "conecta_mysql.inc";

/*        _ Receber dados do usuário _        */
$sql = mysqli_query($con, 'SELECT p.xp,p.hp,p.max_hp,p.energy,p.max_energy FROM characterr as p, user as u WHERE p.user_id = u.id AND u.id = '.$id.'') or die(mysqli_error($con));
$row = mysqli_fetch_row($sql);

/*        _ Fazer update de energia/vida do usuário _        */
$sql = mysqli_query($con, "SELECT l_login_data FROM characterr WHERE user_id = '$id' ") or die(mysqli_error($con));
$lastLogin = mysqli_fetch_row($sql);

date_default_timezone_set("Brazil/East");

$cur_date = date('d-m-Y H:i:s') ; //data atual
$last_l = strtotime($lastLogin[0]); //data no banco de dados em formato 'numérico'
$c_hour = strtotime( $cur_date ); //data atual no formato 'numérico'

$e_l = floor((($c_hour - $last_l)/60)/10); //quantidade de vida/energia a aumentar

$total = (($c_hour - $last_l)/60); //diferença entre a data atual e a data do banco de dados (min + seg)
$min = ($total - (10*$e_l)); //apenas os minutos decorridos desde a ultima atualização (formato 'numérico')
$dif = $min*60; //diferença em segundos
$date = $c_hour - $dif; //horário com o resto
$date = date("d-m-Y H:i:s",$date); //data com o resto da ultima atualização
//Se vida ou energia ainda nao estao no máximo, a hora no BD é atualizada contando o 'resto' da hora anterior
if( ( $row[1] < $row[2] ) || ( $row[3] < $row[4] ) ) {
	mysqli_query($con, " UPDATE characterr SET l_login_data = '$date' WHERE user_id = '$id' ") or die(mysqli_error($con));
	
	//Se a vida dele for menor que a maxima
	if ( $row[1] < $row[2] ) {
		if( ($row[1] + $e_l) < $row[2] ) {
			mysqli_query($con, "UPDATE characterr SET hp = hp + " . $e_l . " WHERE user_id = '$id'") or die(mysqli_error($con));
		} else {
			mysqli_query($con, "UPDATE characterr SET hp = " . $row[2] . " WHERE user_id = '$id'") or die(mysqli_error($con));
		}
	}

	//Se a energia dele for menor que a máxima e a diferença
	if ( $row[3] < $row[4] ) {
		if( ($row[3] + $e_l) < $row[4] ){
			mysqli_query($con, "UPDATE characterr SET energy = energy + " . $e_l . " WHERE user_id = '$id'") or die(mysqli_error($con));
		} else {
			mysqli_query($con, "UPDATE characterr SET energy = " . $row[4] . " WHERE user_id = '$id'") or die(mysqli_error($con));
		}
	}

} else {
	mysqli_query($con, " UPDATE characterr SET l_login_data = '$cur_date' WHERE user_id = '$id' ") or die(mysqli_error($con));
}


$sql = mysqli_query($con, 'SELECT p.xp,p.hp,p.max_hp,p.energy,p.max_energy, p.lvl FROM characterr as p, user as u WHERE p.user_id = u.id AND u.id = '.$id.'') or die(mysqli_error($con));
$row = mysqli_fetch_row($sql);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Piratas do Futuro</title>
        <meta name="description" content="website description" />
        <meta name="keywords" content="website keywords, website keywords" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link href="http://fonts.googleapis.com/css?family=Muli" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Englebert" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="style/style1024.css" media="only screen and (max-width:1245px)"/>
        <link rel="stylesheet" type="text/css" href="style/style1280.css" media="only screen and (min-width: 1246px) and (max-width:1315px)"/>
		<link rel="stylesheet" type="text/css" href="style/style1920.css" media="only screen and (min-width: 1446px) and (max-width:1920px)"/>
        <link rel="stylesheet" type="text/css" href="style/style.css" media="only screen and (min-width:1116px) and (max-width:1445px)"/>
		<script>
			function menuClicked()
			{
				alert("Crie seu personagem primeiro!");
				return false;
			}
		</script>
    <link rel="shortcut icon" href="images/favicon.ico"/> </head>

    <body>
		<div id="main">
			<div id="header">
				<div id="top_menu">
					<ul>
						<li><a href="help.php">Ajuda</a></li>
						<li><a href="about.php">Créditos</a></li>
						<li><a href="logout.php">Sair</a></li>
					</ul>
				</div>
				<div id="user_content">
                    <div id="user_picture_bg">
                    	<div class="user_picture">
				<?php
                                $query = mysqli_query($con, "SELECT ext, data, upload FROM user WHERE id='$id' ") or die(mysqli_error($con));

                                while ($imagens = mysqli_fetch_array($query)){
                                    $dados['ext'] = $imagens['ext'];
                                    $dados['data'] = $imagens['data'];
				    $dados['upload'] = $imagens['upload'];
                                }
				if($dados['upload'] == 0)
					echo '<img src="data:image/' . $dados['ext'] . ';base64,' . base64_encode( $dados['data'] ) . '" width="110" height="125" /></a>';
				else
                                	echo "<img src='fotos_perfil/".$dados['data']."' width='140' height='145' />";
                                ?>
                         </div>
					</div>
					<div id="user_data">
						<div id="user_data_images">
								<img src="images/life.png" width="80%" height="30%" />
								<img src="images/energy.png" width="80%" height="30%" />
								<img src="images/exp.png" width="85%" height="30%" />
						</div>
                        <div id="user_life">
        					<?php echo $row[1] ?>/<?php echo $row[2] ?>
                        </div>
                        <div id="user_energy">
        					<?php echo $row[3] ?>/<?php echo $row[4] ?>
                        </div>
                        <div id="user_xp">
    						<?php echo $row[0] ?>
                        </div>
                    </div>
				</div>
				<div id="menubar">
					<?php
				$sql = mysqli_query($con, "SELECT user_id FROM characterr WHERE user_id = '$id'");

				/*        _ Verifica se é o primeiro login do Aluno _        */
				if (mysqli_num_rows($sql) != 1)
				{
					$html = '';
					$html .= '<ul>';
					$html .= '<li><a href="index.php">In&iacute;cio</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Status</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Mapa</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Arena</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Desafios</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Miss&otilde;es</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Invent&aacute;rio</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Loja</a></li>';
					$html .= '<li><a href="#" onclick="menuClicked()" >Ranking</a></li>';
					$html .= '</ul>';
				}
				else
				{
					$html = '';
					$html .= '<ul>';
					$html .= '<li><a href="index.php">Início</a></li>';
					$html .= '<li><a href="db_character.php">Status</a></li>';
					$html .= '<li><a href="map.php">Mapa</a></li>';
					$html .= '<li><a href="db_arena.php">Arena</a></li>';
					$html .= '<li><a href="db_challenges.php">Desafios</a></li>';
					$html .= '<li><a href="db_missions.php">Missões</a></li>';
					$html .= '<li><a href="db_inventory.php">Inventário</a></li>';
					$html .= '<li><a href="db_store.php">Loja</a></li>';
					$html .= '<li><a href="db_ranking.php">Ranking</a></li>';
					$html .= '</ul>';
				}
				echo $html;
				?>
				</div>
			</div>
			<div id="content">
				<div id="text">
							<h1>Guia para ajuda</h1>
							
                        	<ul>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_history.php"> Conheça o enredo do jogo</p></a></li></img>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_register.php"> Ainda não efetuei o cadastro do meu personagem</p></a></li>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_tutorial.php"> Tutorial de como jogar</p></a></li>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_submit.php"> Como submeter um desafio</p></a></li>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_util.php"> Utilidades</p></a></li>
                                <li><p><img width="10px" height="10px" src="images/marcador.png"><a href="h_video.php"> Video de divulgação</p></a></li>
								<li><p><img width="10px" height="10px" src="images/marcador.png"><a href="contact.php"> Fale conosco</p></a></li>
							</ul>
						</div>
					</div>
		</div> 
	</body>
</html>