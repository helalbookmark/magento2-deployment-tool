<?php

/**
 * askProjectPropertiesTask
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */
require_once "phing/Task.php";
require_once "phing/input/InputRequest.php";

class askProjectPropertiesTask extends Task
{
    protected $propertiesToAsk = [
        "git.repo.url" => [],
        "live.symlink" => [],
        "magento.dir" => [],
        "opcache.enabled" => [1, 0],
        "varnish.enabled" => [1, 0],

    ];

    public function main()
    {
        foreach ($this->propertiesToAsk as $property => $validValues) {
            $inputValue = $this->promptProperty($property, $validValues);
            $this->project->setUserProperty($property, $inputValue);
        }
    }

    protected function promptProperty($property, array $validValues)
    {
        $currentValue = $this->project->getProperty($property);
        $promptText = $this->getPromptTextToAsk($property, $currentValue, $validValues);
        do {
            $inputValue = $this->getInputRequest($promptText);
            if ($inputValue === "") {
                $inputValue = $currentValue;
            }
            $isValid = $this->isInputValid($inputValue, $validValues);
        } while (!$isValid);

        return $inputValue;
    }

    protected function getPromptTextToAsk($property, $currentValue = "", array $validValues = [])
    {
        $promptText = sprintf('%s:', $property);
        if ("" !== $currentValue && null !== $currentValue) {
            $promptText .= sprintf(' [%s]', $currentValue);
        }
        if ($validValues) {
            $promptText .= sprintf(' (%s)', implode(', ', $validValues));
        }
        return $promptText;
    }

    protected function getInputRequest($promptText)
    {
        $request = new InputRequest($promptText);
        $this->project->getInputHandler()->handleInput($request);
        return $request->getInput();
    }

    protected function isInputValid($inputValue, array $validValues)
    {
        if (!$validValues && "" !== $inputValue) {
            return true;
        }
        if (in_array($inputValue, $validValues)) {
            return true;
        }
        return false;
    }
}