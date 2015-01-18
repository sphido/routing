<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';

class Test {
	/** @var bool */
	public $call = false;

	/** @var array|null */
	public $args;

	function __invoke() {
		$this->args = func_get_args();
		$this->call = true;
	}
}

{ // use all handler if is set
	$_SERVER['REQUEST_URI'] = '/call-all-handler';
	$_SERVER['REQUEST_METHOD'] = 'GET';

	map($all = new Test);
	map(404, $notFound = new Test); // setup 404 handler
	dispatch();
	Assert::true($all->call);
	Assert::false($notFound->call);
}

{ // disable all handler by routes function
	$_SERVER['REQUEST_URI'] = '/call-something-else';
	$_SERVER['REQUEST_METHOD'] = 'GET';

	map($all = new Test);
	routes()->all = null; // disable current all handler
	map(404, $notFound = new Test); // setup 404 handler
	dispatch();
	Assert::false($all->call);
	Assert::true($notFound->call);
}

http_response_code(200); // fix exit code