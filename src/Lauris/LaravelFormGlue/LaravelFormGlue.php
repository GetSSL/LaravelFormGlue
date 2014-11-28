<?php

namespace Lauris\LaravelFormGlue;

abstract class LaravelFormGlue {

	public $items = [];
	public $rules = [];
	public $validator = null;
	
	abstract public function buildForm();

	public function __construct($url, $method = "POST") 
	{
		$this->items[] = \Form::open(["url" => $url, "method" => $method]);
		$this->buildForm();
		$this->items[] = \Form::close();
	}

	public function getData()
	{
		return $this->data;
	}

	public function setRules($name, $rules)
	{
		if (!empty($rules) && !empty($name)) {
			$this->rules[$name] = $rules;
		}
	}
	
	public function add($field, $name, $label, $options)
	{
		$elements = [];
		if ($label) {
			if (!is_string($label)) {
				$label = ucfirst($name);
			}
			$elements[] = \Form::label($name, $label);
		}
		$elements[] = $field;

		$body = implode(PHP_EOL, $elements);
		$this->items[] = $this->wrap($body);

		$this->setRules($name, array_get($options, "rules", null));
	}

	public function text($name, $options = array())
	{
		$value = array_get($options, "value", null);
		$label = array_get($options, "label", $name);

		$field = \Form::text($name, $value, ["class" => "form-control"]);
		$this->add($field, $name, $label, $options);
		return $this;
	}

	public function select($name, $options = array())
	{
		$values = array_get($options, "values", array());
		$value = array_get($options, "value", null);
		$label = array_get($options, "label", $name);

		$field = \Form::select($name, $values, $value, ["class" => "form-control"]);
		$this->add($field, $name, $label, $options);
		return $this;
	}

	public function submit($value = null)
	{
		$field = \Form::submit($value, ["class" => "btn btn-primary"]);
		$this->add($field, null, false, null);
		return $this;
	}

	public function wrap($element)
	{
		return "<div class=\"form-group\">$element</div>";
	}

	public function validate($data)
	{
		$this->data = $data;
		$this->validator = \Validator::make($data, $this->rules);
		return $this->validator->passes();
	}

	public function render()
	{
		return implode(PHP_EOL, $this->items);
	}
}