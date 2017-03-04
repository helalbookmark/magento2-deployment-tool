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
    /**
     * property_name => [property_data]
     * @var array
     */
    protected $projectProperties = [
        "git.repo.url" => ['description' => 'Project git url to clone from'],
        "live.symlink" => ['description' => 'Target dir where live release will be symlinked'],
        "magento.dir" => ['description' => 'Magento dir into the project root. Set "." if magento is installed on project root'],
        "opcache.enabled" => ['valid_values' => [1, 0]],
        "varnish.enabled" => ['valid_values' =>[1, 0]],
        "static-content.languages" => ['description' => 'Space-separated list of language codes to deploy']
    ];

    public function main()
    {
        $this->log("Input project properties");
        foreach ($this->projectProperties as $property => $propertyData) {
            $inputValue = $this->promptProperty($property, $propertyData);
            $this->project->setUserProperty($property, $inputValue);
        }
        $this->exportProjectProperties();
    }

    /**
     * @param $property
     * @param array $propertyData
     * @return string
     */
    protected function promptProperty($property, array $propertyData)
    {
        $defaultValue = $this->project->getProperty($property);
        $promptText = $this->getPromptTextToAsk($property, $defaultValue, $propertyData);
        $promptText = PHP_EOL . $promptText;
        do {
            $inputValue = $this->getInputRequest($promptText);
            if ($inputValue === "") {
                $inputValue = $defaultValue;
            }
            $isValid = $this->isInputValid($inputValue, $propertyData);
        } while (!$isValid);

        return $inputValue;
    }

    /**
     * @param $property
     * @param string $defaultValue
     * @param array $propertyData
     * @return string
     */
    protected function getPromptTextToAsk($property, $defaultValue = "", array $propertyData = [])
    {
        $promptText = "";
        if (isset($propertyData['description'])) {
            $promptText .= $propertyData['description'] . PHP_EOL;
        }
        $promptText .= sprintf('-> %s:', $property);
        if ("" !== $defaultValue && null !== $defaultValue) {
            $promptText .= sprintf(' [%s]', $defaultValue);
        }
        if (isset($propertyData['valid_values']) && is_array($propertyData['valid_values'])) {
            $promptText .= sprintf(' (%s)', implode(', ', $propertyData['valid_values']));
        }
        return $promptText;
    }

    /**
     * @param $promptText
     * @return mixed
     */
    protected function getInputRequest($promptText)
    {
        $request = new InputRequest($promptText);
        $this->project->getInputHandler()->handleInput($request);
        return $request->getInput();
    }

    /**
     * @param $inputValue
     * @param array $propertyData
     * @return bool
     */
    protected function isInputValid($inputValue, array $propertyData)
    {
        $validValues = $propertyData['valid_values']??null;
        if (!$validValues && "" !== $inputValue) {
            return true;
        }
        if (is_array($validValues) && in_array($inputValue, $validValues)) {
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

    /**
     * @param $content
     * @param $targetFile
     * throw BuildException
     */
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