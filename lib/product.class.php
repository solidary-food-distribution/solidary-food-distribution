<?php

declare(strict_types=1);

require_once('member.class.php');

class Product{
  public int $id;
  public string $name;
  public Member $producer;
  public string $type;
  public string $period;
  public float $amount_steps;
  public float $amount_min;
  public float $amount_max;
  public string $status;
  public string $orders_lock_date; //REFACTOR DateTime
  public float $price;
  public float $tax;
  public bool $tax_incl;

}
