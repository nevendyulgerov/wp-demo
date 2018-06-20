/* globals ammo */

/**
 * App entry-point
 */

(() => {
  const props = {
    global: true,
    name: 'symmetry',
  };

  const app = ammo.app(props).schema('app').augment('admin');

  app.configure('events')
    .node('onReady', callback => ammo.onDomReady(callback));

  app.configure('actions')
    .node('init', () => {
      const {events, core} = app.getNodes();

      events.onReady(() => {
        const manager = core.manager();
        manager.runCommon().runComponents();
      });
    });

  app.callNode('actions', 'init');
})();
