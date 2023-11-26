<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Imperial</title>
    <style>
        .cabecalho nav ul li a.user {
            color: white;
        }
        .cabecalho nav ul a.logout {
            color: red;
        }
    </style>
</head>

<body>
    <header class="cabecalho">
        <nav>
            <ul class="ListNav">
                <li><a href="#sobreNos">SOBRE NÓS</a></li>
                <li><a href="#servicos">SERVIÇOS</a></li>
                <a href="index.php">
                    <img class="Logo" src="assets/Logo.jpeg" alt="Logo Imperial">
                </a>
                <li><a href="agendamento/agendar.php">AGENDAMENTO</a></li>
                <?php
                    // Verifica se o usuário está logado
                    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                        // Se estiver logado, mostra o nome do usuário
                        echo '<li><a class="user" id="userLink">'. strtoupper($_SESSION["usuario"]).'</a></li>';
                        // Adiciona o botão LOGOUT, inicialmente oculto
                        echo '<a href="login/logout.php" class="logout" id="logoutLink" style="display: none;">LOGOUT</a>';
                    } else {
                        // Se não estiver logado, mostra o botão de login
                        echo '<li><a href="login/login.php">LOGIN</a></li>';
                    }
                ?>
            </ul>
        </nav>
    </header>
    <div class="principal">

        <main>
            <div class="Titulo">
                <h1>BEM VINDO À IMPERIAL</h1>
            </div>

            <div class="divisao" id="sobreNos">
                <h2>SOBRE NÓS</h2>
            </div>

            <div class="sobreNos">
                <p>Texto aqui sobre a barbeariablabla balbalbbal balba l b a lba lba l b a l a bl a b l a b b a la b l a
                    bl a b labl a bl abal b la bl ab a lbalbalbalba lbl abalbl ablabalbalbal balbalbalb alb a l b a l a
                    b l a b l a b lab l a b l a b a l b a l b a l b a l b albaa</p>
                <img class="Img" src="" alt="ImagemSobreNos">
            </div>

            <div class="localizacao">
                <p>Venha visitar a barbearia! Estamos localizados na rua blabalbalbalba</p>
                <!-- Div para o mapa -->
                <div id="mapa" style="height: 400px;"></div>
            </div>

            <div class="divisao" id="servicos">
                <h2>SERVIÇOS</h2>
            </div>
            <div class="servicos">
                <div class="servico">
                    <p class="nomeServico">CORTE</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
                <div class="servico">
                    <p class="nomeServico">BARBA</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
                <div class="servico">
                    <p class="nomeServicoLongo">CORTE E BARBA</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
            </div>
            <div class="servicos">
                <div class="servico">
                    <p class="nomeServicoLongo">SOBRANCELHA</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
                <div class="servico">
                    <p class="nomeServico">balbalbla</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
                <div class="servico">
                    <p class="nomeServicoLongo">balblabalbalbal</p>
                    <img class="imagemServico" src="assets/Corte.png" alt="">
                    <p class="precoServico">R$ 25,00</p>
                </div>
            </div>
        </main>
    </div>
    <div class="rodape">
        <img class="Logo" src="assets/Logo.jpeg">
        <div class="linha">
            <img class="icon" src="assets/instagram.png" alt="">
            <p>@imperial.barbearia_</p>
        </div>
        <div class="linha">
            <img class="icon" src="assets/whatsapp.png" alt="">
            <p>(11) 97283-1827</p>
        </div>
        <div class="linha">
            <img class="icon" src="assets/mail.png" alt="">
            <p>imperial.barbearia@gmail.com</p>
        </div>
    </div>


    <!-- Adicionando script da API do Google Maps -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSiJC5CmRMKpD4IvwMklOppFXDkWdy9zA&callback=inicializarMapa"></script>

    <script>
        function inicializarMapa() {
            // Coordenadas da sua localização no Google Maps
            var coordenadas = { lat: -23.550520, lng: -46.633308 };

            // Opções do mapa
            var opcoesMapa = {
                zoom: 14,
                center: coordenadas
            };

            var mapa = new google.maps.Map(document.getElementById('mapa'), opcoesMapa);

            var marcador = new google.maps.Marker({
                position: coordenadas,
                map: mapa,
                title: 'Imperial Barbearia'
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Função para rolar suavemente para uma seção
            function scrollSmoothly(targetId) {
                var targetElement = document.getElementById(targetId);
                var headerHeight = document.querySelector('.cabecalho').offsetHeight;
                var targetPosition = targetElement.offsetTop - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }

            // Adiciona um ouvinte de evento para o botão "SOBRE NÓS"
            document.querySelector('a[href="#sobreNos"]').addEventListener('click', function (e) {
                e.preventDefault();
                scrollSmoothly('sobreNos');
            });

            // Adiciona um ouvinte de evento para o botão "SERVIÇOS"
            document.querySelector('a[href="#servicos"]').addEventListener('click', function (e) {
                e.preventDefault();
                scrollSmoothly('servicos');
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para alternar a visibilidade do botão de logout
            function toggleLogoutButton() {
                var logoutButton = document.getElementById('logoutLink');
                // Se o botão de logout estiver visível, oculta; se estiver oculto, mostra
                logoutButton.style.display = (logoutButton.style.display === 'none') ? 'block' : 'none';
            }

            // Adiciona um ouvinte de evento para o link do usuário
            document.getElementById('userLink').addEventListener('click', function (e) {
                e.preventDefault();
                // Chama a função para alternar a visibilidade do botão de logout
                toggleLogoutButton();
            });
        });
    </script>
</body>

</html>