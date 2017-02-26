<?php

/**
 * ProjectPropertiesTask
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */
require_once "phing/Task.php";
require_once "phing/input/InputRequest.php";
require_once 'phing/system/io/PhingFile.php';

class ProjectPropertiesTask extends Task
{
    protected $projectProperties = [
        "git.repo.url" => [],
        "live.symlink" => [],
        "magento.dir" => [],
        "opcache.enabled" => [1, 0],
        "varnish.enabled" => [1, 0],

    ];

    public function main()
    {
        $this->log("Input project properties");
        foreach ($this->projectProperties as $property => $validValues) {
            $inputValue = $this->promptProperty($property, $validValues);
            $this->project->setUserProperty($property, $inputValue);
        }
        $this->exportProjectProperties();

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

    protected function exportProjectProperties()
    {
        $propertiesText = "";
        $propertiesNames = array_keys($this->projectProperties);
        foreach ($propertiesNames as $propertyName) {
            $propertiesText .= $propertyName . "=" . $this->project->getProperty($propertyName) . PHP_EOL;
        }
        $projectPropertiesFile = sprintf("%s/deployment-settings/project.properties", $this->project->getProperty("application.startdir"));
        $this->putContentInFile($propertiesText, $projectPropertiesFile);
        $this->log(sprintf("Properties saved in: %s", $projectPropertiesFile));
    }

    protected function putContentInFile($content, $targetFile)
    {
        $parentDir = new PhingFile(dirname($targetFile));
        if (!$parentDir->exists()) {
            $parentDir->mkdirs(0755);
        }
        if (!file_put_contents($targetFile, $content)) {
            throw new BuildException('Failed writing to ' . $targetFile);
        }
    }
}