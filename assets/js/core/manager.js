/* globals ammo, symmetry */

/**
 * Core: Router
 */

symmetry.addNode('core', 'manager', () => {
  const app = symmetry;
  const props = {};
  const store = {processed: []};
  const module = ammo.app(props, store).schema('default');

  module.configure('actions')
    .node('normalizeProps', props => {
      const normalizedProps = {};
      ammo.eachKey(props, (val, key) =>
        normalizedProps[key] = ammo.convertByType(val));

      return normalizedProps;
    })
    .node('removeModules', (persistentModules = []) => {
      ammo.selectAll('[data-module]')
        .filter(module => persistentModules.indexOf(module.getAttribute('data-module')) === -1)
        .each(module => ammo.removeEl(module));
    })
    .node('domContainsModule', moduleName => {
      const module = ammo.select(`[data-module='${moduleName}']`).get();
      return ammo.isObj(module);
    })
    .node('getViewName', () => {
      const view = ammo.select('[data-module][data-view]').get();
      return view.getAttribute('data-module');
    })
    .node('runCommon', () => {
      const common = app.getNodes('common');
      ammo.runMethodsInParallel(common);
    })
    .node('runAdmin', () => {
      const admin = app.getNodes('admin');
      ammo.runMethodsInParallel(admin);
    })
    .node('runComponents', () => {
      const components = app.getNodes('components');

      ammo.selectAll('[data-component]').each($component => {
        const componentName = $component.getAttribute('data-component');

        if (ammo.hasMethod(components, componentName)) {
          components[componentName]($component, ammo.app().schema('component'));
        }
      });
    });

  return {
    runCommon() {
      module.callNode('actions', 'runCommon');
      return this;
    },
    runAdmin() {
      const { admin } = app.getNodes('admin');
      admin();
      return this;
    },
    runComponents() {
      module.callNode('actions', 'runComponents');
      return this;
    },
    removeModules(persistentModules) {
      module.getNode('actions', 'removeModules')(persistentModules);
      return this;
    },
    notifyBodyUponViewLoading() {
      module.getNode('actions', 'notifyBodyUponViewLoading')(
        module.getNode('actions', 'getViewName'));
      return this;
    },
    domContainsModule: module.getNode('actions', 'domContainsModule'),
    getViewName: module.getNode('actions', 'getViewName'),
  };
});
