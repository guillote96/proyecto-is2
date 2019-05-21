<?php


class AuctionsView extends TwigView {

  public function show($datos) {

    echo self::getTwig()->render('auctions.html', $datos);


  }


}