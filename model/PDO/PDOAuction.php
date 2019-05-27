<?php


class PDOAuction extends PDORepository {

  private static $instance;

  public static function getInstance()
  {

    if (!isset(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function __construct()
  {

  }


  /**
   * Returns all the auctions from the database with the associated information.
   *
   * @return array of AuctionDetail
   */
  public function getDetailedAuctions()
  {
    $auctions = $this->queryList("
        SELECT s.idSubasta, s.base, s.activa, r.titulo, sem.fecha_inicio, sem.fecha_fin
        FROM subasta s
        INNER JOIN residencia_semana rs on s.idResidenciaSemana = rs.idResidenciaSemana
        INNER JOIN residencia r on rs.idResidencia = r.idResidencia
        INNER JOIN semana sem on rs.idSemana = sem.idSemana", []
    );


    $usersPerAuction = array_map(function ($auction) {
      $users = $this->queryList("
        SELECT u.idUsuario, ps.puja
        FROM usuario u
        INNER JOIN  participa_subasta ps on u.idUsuario = ps.idUsuario", []
      );

      $bids = array_map(function ($user) {return (float) $user["puja"];}, $users);
      $max = 0;
      foreach ($bids as $bid){
        if ($bid > $max){
          $max = $bid;
        }
      }
      $currentAmount = (float) $max;

      return [ "auction" => $auction, "users" => $users, "currentAmount" => $currentAmount];
    }, $auctions);

    return array_map(function ($auctionInfo) {

      $auctionId = $auctionInfo["auction"]["idSubasta"];
      $active = $auctionInfo["auction"]["activa"];
      $base = $auctionInfo["auction"]["base"];
      $currentAmount = $auctionInfo["currentAmount"];
      $week = ["from_date" => $auctionInfo["auction"]["fecha_inicio"], "to_date" => $auctionInfo["auction"]["fecha_fin"]];
      $residence = $auctionInfo["auction"]["titulo"];


      return new AuctionDetail($auctionId, $active, $base, $currentAmount, $week, $residence);
    }, $usersPerAuction);
  }


}
