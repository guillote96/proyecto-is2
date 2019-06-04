<?php


class AuctionDetail {

  private $auctionId;
  private $active;
  private $base;
  private $currentAmount;
  private $week;
  private $residence;
  private $borrada;

  /**
   * AuctionDetail constructor.
   * @param $auctionId
   * @param $active
   * @param $base
   * @param $currentAmount
   * @param $week
   * @param $residence
   */
  public function __construct($auctionId, $active, $base, $currentAmount, $week, $residence,$borrada)
  {
    $this->auctionId = $auctionId;
    $this->active = $active;
    $this->base = $base;
    $this->currentAmount = $currentAmount;
    $this->week = $week;
    $this->residence = $residence;
     $this->borrada = $borrada;
  }

  /**
   * @return mixed
   */
  public function getAuctionId()
  {
    return $this->auctionId;
  }

  /**
   * @param mixed $auctionId
   */
  public function setAuctionId($auctionId): void
  {
    $this->auctionId = $auctionId;
  }

  /**
   * @return mixed
   */
  public function getActive()
  {
    return $this->active;
  }

  /**
   * @param mixed $active
   */
  public function setActive($active): void
  {
    $this->active = $active;
  }

  /**
   * @return mixed
   */
  public function getBase()
  {
    return $this->base;
  }

  /**
   * @param mixed $base
   */
  public function setBase($base): void
  {
    $this->base = $base;
  }

  /**
   * @return mixed
   */
  public function getCurrentAmount()
  {
    return $this->currentAmount;
  }

  /**
   * @param mixed $currentAmount
   */
  public function setCurrentAmount($currentAmount): void
  {
    $this->currentAmount = $currentAmount;
  }

  /**
   * @return mixed
   */
  public function getWeek()
  {
    return $this->week;
  }

  /**
   * @param mixed $week
   */
  public function setWeek($week): void
  {
    $this->week = $week;
  }

  /**
   * @return mixed
   */
  public function getResidence()
  {
    return $this->residence;
  }

  /**
   * @param mixed $residence
   */
  public function setResidence($residence): void
  {
    $this->residence = $residence;
  }

   public function getBorrada()
  {
    return $this->borrada;
  }




}