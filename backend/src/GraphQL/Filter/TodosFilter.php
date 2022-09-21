<?php

namespace App\GraphQL\Filter;

use Interweber\GraphQL\Filter\Filter;
use Interweber\GraphQL\Filter\Matcher\StringMatcher;
use TheCodingMachine\GraphQLite\Annotations\Factory;

class TodosFilter extends Filter {
	#[Factory]
	public static function factory(
		?StringMatcher $title
	): TodosFilter {
		return new static([
			'title' => $title
		]);
	}
}
