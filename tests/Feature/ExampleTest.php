<?php

test('la pagina principale risponde correttamente', function () {
    //Invia una richiesta HTTP GET alla pagina principale
    $response = $this->get('/');

    //Verifica che l’applicazione risponda con il codice HTTP 200
    //che indica una richiesta completata correttamente
    $response->assertStatus(200);
});
