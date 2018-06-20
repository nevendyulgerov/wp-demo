/* globals symmetry, ammo */

symmetry.addNode("components", "project", ($component, component) => {
  "use strict";

  // const component = ammo.app().schema("component");

  component.configure("actions")
    .node("init", () => {
      const nodes = component.getNodes();
    });

  component.callNode("actions", "init");
});