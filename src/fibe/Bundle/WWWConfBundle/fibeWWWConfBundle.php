<?php

namespace fibe\Bundle\WWWConfBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class fibeWWWConfBundle extends Bundle
{
	public function getParent()
	{
		return 'IDCISimpleScheduleBundle' ;
	}
}

?>