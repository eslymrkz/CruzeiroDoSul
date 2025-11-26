<?php
$weatherKey = '671c12f7ce4409d98b051b3d007819fe'; 
$gnewsKey   = 'ed004e1dc8e1f6dedd6d39783bd32000';
$cidade     = 'Joao Pessoa';
$pais       = 'br';
// Usamos cURL para evitar bloqueios de servidor
function buscarDados($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MeuPortalPHP/1.0');
    $resultado = curl_exec($ch);
    curl_close($ch);
    return json_decode($resultado, true);}
// 1. BUSCAR CLIMA
$urlClima = "https://api.openweathermap.org/data/2.5/weather?q={$cidade},{$pais}&appid={$weatherKey}&units=metric&lang=pt_br";
$dadosClima = buscarDados($urlClima);
$climaTemp = '--';
$climaDesc = 'Atualizando...';
$climaIcon = '';
$climaCidade = $cidade;
if (isset($dadosClima['main'])) {
    $climaTemp = round($dadosClima['main']['temp']);
    $climaDesc = ucfirst($dadosClima['weather'][0]['description']);
    $climaIcon = "https://openweathermap.org/img/wn/" . $dadosClima['weather'][0]['icon'] . "@2x.png";
    $climaCidade = $dadosClima['name'];}
// 2. BUSCAR NOTÍCIAS
$urlNews = "https://gnews.io/api/v4/top-headlines?category=general&lang=pt&country={$pais}&max=5&apikey={$gnewsKey}";
$dadosNews = buscarDados($urlNews);
$listaNoticias = $dadosNews['articles'] ?? [];
// 3. DATA HOJE
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$dias = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
$meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$hoje = $dias[date('w')] . ', ' . date('j') . ' de ' . $meses[date('n')-1];
?>

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel: <?php echo $cidade; ?></title>
    <style>
    /* CSS */
        :root { --primary: #007bff; --bg: #f0f2f5; --card: #fff; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: var(--bg); color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        /*CABEÇALHO*/
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logo { font-size: 1.5rem; font-weight: bold; color: #333; }
        .date-badge { background: #e4e6eb; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; color: #555; }
        /*CLIMA*/
        .weather-widget {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 30px; border-radius: 20px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 10px 25px rgba(118, 75, 162, 0.3); margin-bottom: 40px;}
        .w-info h1 { margin: 0; font-size: 3.5rem; font-weight: 700; letter-spacing: -2px; }
        .w-info p { margin: 0; font-size: 1.2rem; opacity: 0.9; }
        .w-icon img { width: 100px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3)); }
        .w-desc { text-align: right; font-size: 1.1rem; text-transform: capitalize; }
        /*NOTICIAS*/
        .news-header { display: flex; align-items: center; margin-bottom: 20px; gap: 10px; }
        .news-header h2 { margin: 0; font-size: 1.4rem; }
        .news-grid { display: grid; gap: 20px; }
        .news-card {
            background: var(--card); border-radius: 12px; overflow: hidden;
            display: flex; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none; color: inherit;
        }
        .news-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .n-img { width: 140px; min-height: 100px; object-fit: cover; background: #ddd; }
        .n-content { padding: 15px; display: flex; flex-direction: column; justify-content: center; }
        .n-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; line-height: 1.4; color: #1a1a1a; }
        .n-meta { font-size: 0.85rem; color: #666; }
        .n-source { color: var(--primary); font-weight: 600; }
        @media (max-width: 600px) {
            .weather-widget { flex-direction: column; text-align: center; gap: 10px; }
            .w-desc { text-align: center; }
            .news-card { flex-direction: column; }
            .n-img { width: 100%; height: 180px; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">Meu Painel</div>
        <div class="date-badge"><?php echo $hoje; ?></div>
    </header>

    <div class="weather-widget">
        <div class="w-info">
            <h1><?php echo $climaTemp; ?>°</h1>
            <p><?php echo $climaCidade; ?></p>
        </div>
        <div class="w-icon">
            <?php if($climaIcon): ?>
                <img src="<?php echo $climaIcon; ?>" alt="Clima">
            <?php endif; ?>
            <div class="w-desc"><?php echo $climaDesc; ?></div>
        </div>
    </div>

    <div class="news-header">
        <h2>📰 Notícias do Dia</h2>
    </div>

    <div class="news-grid">
        <?php if (!empty($listaNoticias)): ?>
            <?php foreach ($listaNoticias as $news): ?>
                <?php 
                    $foto = !empty($news['image']) ? $news['image'] : 'https://via.placeholder.com/300x200?text=Sem+Foto';
                    $fonte = $news['source']['name'];
                    $hora = date('H:i', strtotime($news['publishedAt']));
                ?>
                <a href="<?php echo $news['url']; ?>" target="_blank" class="news-card">
                    <img src="<?php echo $foto; ?>" alt="Foto" class="n-img">
                    <div class="n-content">
                        <div class="n-title"><?php echo $news['title']; ?></div>
                        <div class="n-meta">
                            <span class="n-source"><?php echo $fonte; ?></span> • <?php echo $hora; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; padding: 40px; color: #888;">
                Nenhuma notícia carregada. Verifique se a API Key da GNews está correta.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>