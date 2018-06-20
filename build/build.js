
const fs = require("fs");
const titlize = (text, format) => {
  const textWords = text.split("-");
  if (format === "camel") {
    return textWords.map((word, index) => index > 0
      ? word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
      : word.charAt(0).toLowerCase() + word.slice(1).toUpperCase()).join("");
  } else if (format === "pascal") {
    return textWords.map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join("");
  }
};

const createFile = (componentName, path, fileExtension, content, fileName = "index") => {
  if (fs.existsSync(path)) {
    return console.error(`Error! File [${fileName}.${fileExtension}] for Component [${componentName}] already exists.`);
  }
  fs.mkdirSync(path);

  fs.writeFile(`${path}/${fileName}.${fileExtension}`, content, (err) => {
    if ( err ) {
      return console.log(err);
    }
  });
};

const createJavaScriptFile = (appName, componentName) => {
  const componentPath = `./components/${componentName}/script`;
  const content = `
/* globals ${appName}, ammo */

${appName}.addNode("components", "${componentName}", ($component, component) => {

  component.configure("actions")
    .node("init", () => {
      const nodes = component.getNodes();
    });

  component.callNode("actions", "init");
});

`.trim();

  createFile(componentName, componentPath, "js", content);
};

const createStyleFile = componentName => {
  const componentPath = `./components/${componentName}/sass`;
  const content = `
/**
 * Component: ${titlize(componentName, 'pascal')}
 */

[data-component="${componentName}"] {

}

`.trim();

  createFile(componentName, componentPath, "scss", content);
};

const createPhpScript = componentName => {
  const componentPath = `./components/${componentName}`;
  const content = `
<?php
namespace Symmetry;

class ${titlize(componentName, "pascal")} extends Component {

	/**
	 * @description Render
	 * @param string $component
	 * @param array $args
	 */
	public static function render($args = array(), $component = '') {
		parent::render(basename(__DIR__), $args);
	}
}

`.trim();

  createFile(componentName, componentPath, "php", content, titlize(componentName, "pascal"));
};

const createHtmlTemplate = componentName => {
  const componentPath = `./components/${componentName}/templates`;
  const content = `
  <?php
/**
 * Component: ${titlize(componentName, "pascal")}
 * Template: Default
 * @param {object} $templateArgs
 */
$templateData = $templateArgs->data;
?>

<div data-component="${componentName.toLowerCase()}" data-template="default">
	<?php echo __('Component [${componentName.toLowerCase()}]'); ?>
</div>

`.trim();

  createFile(componentName, componentPath, "php", content, "default");
};

exports.createComponent = (appName, componentName) => {
  createPhpScript(componentName);
  createJavaScriptFile(appName, componentName);
  createStyleFile(componentName);
  createHtmlTemplate(componentName);
};
