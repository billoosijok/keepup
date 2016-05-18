<?php 
	class A {
		public $a = '';
		
		function __construct($a) {
			$this->a = $a;
		}
	}

	class B extends A {
		function name() {
			echo $this->a;
		}
	}
	$a = new A('yay');
	$b = new B('yayy');
	$b->name();
?>

