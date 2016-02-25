<?php
namespace SoftwareEngineerTest;

/**
  * Class Customer
  * 
  * @author   Aristide Asprogerakas <aristideau@gmail.com>
  * @property int $id Customer id
  * @property float $balance Customers balance
  * @property string $userName Customers system generated username
  */
abstract class Customer {
	protected $id;
	protected $balance = 0;
	protected $userName = '';

	public function __construct($id) {
		$this->id = $id;
	}

/**
 * Generates the customers username
 * Valid characters are A-Z,a-z,0-9
 *
 * @param int $len length of the username, min 10 characters, max 30 characters
 * @return string generated user name than begins with B,S or G based on the calling class
 */	
	public function generate_username($len = 10) {
		$len = ($len < 10 ? 10 : $len);
		$len = ($len > 28 ? 29 : $len);
		$validChar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$this->userName = '';
		for ($i = 0; $i < $len - 1; $i++) {
			$this->userName .= $validChar[rand(0, strlen($validChar) - 1)];
		}
		$this->userName = substr(end(explode('\\', get_called_class())), 0, 1) . $this->userName;
		return $this->userName;
	}		
	
/**
 * Returns the customers current balance
 * @return float Customers current balance  
 */	
	public function get_balance() {
		return $this->balance;
	}	
}


/**
  * Class Bronze 
  * 
  * @author   Aristide Asprogerakas <aristideau@gmail.com>
  * @method void deposit(float $amount) Adds funds to customers balance with a 5% bonus
  */	
class Silver extends Customer {
	public function deposit($amount) {
		$this->balance += $amount * 1.05;
	}
}
/**
  * Class Bronze 
  * 
  * @author   Aristide Asprogerakas <aristideau@gmail.com>
  * @method void deposit(float $amount) Adds funds to customers balance with a 10% bonus
  */
class Gold extends Customer {
	public function deposit($amount) {
		$this->balance += $amount * 1.1;
	}
}

/**
  * Class Bronze 
  * 
  * @author   Aristide Asprogerakas <aristideau@gmail.com>
  * @method void deposit(float $amount) Adds funds to customers balance
  */
class Bronze extends Customer {
	public function deposit($amount) {
		$this->balance += $amount;
	}	
}

/**
 * Class CustomerFactory
 * Returns one of three customer types (Gold, Silver or Bronze) based on the user id
 * If the first letter is G a Gold() customer is returned
 * If the first letter is B a Bronze() customer is returned
 * If the first letter is S a Silver() customer is returned
 * If the id does not begin with one of these letters, or is greater than 10 characters long
 * an exception is raised
 * @author   Aristide Asprogerakas <aristideau@gmail.com>
 * @param string $id The customers ID 
 */
class CustomerFactory {
	public static function get_instance($id) {
		if (preg_match("/^[G][0-9]{0,9}$/", $id)) {
			return new Gold($id);
		} elseif (preg_match("/^[S][0-9]{0,9}$/", $id)) {
			return new Silver($id);
		} elseif (preg_match("/^[B][0-9]{0,9}$/", $id)) {
			return new Bronze($id);
		} else {
			throw new InvalidArgumentException('Invalid Customer ID, ID must begin with the letters B,S or G followed by up to 9 digits');
		}
	} 
}

// Testing code
$B = CustomerFactory::get_instance("B00000000");
$S = CustomerFactory::get_instance("S00000000");
$G = CustomerFactory::get_instance("G123456789");
//$G = CustomerFactory::get_instance("G123z456789"); // Generates exception

echo $B->generate_username(7).'<br>';
echo $S->generate_username(6).'<br>';
echo $G->generate_username(5).'<br>';

$B->deposit(100);
$S->deposit(100);
$G->deposit(100);

echo $B->get_balance().'<br>';
echo $S->get_balance().'<br>';
echo $G->get_balance().'<br>';
