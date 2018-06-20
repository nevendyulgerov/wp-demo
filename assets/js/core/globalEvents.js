/* globals symmetry */

/**
 * Core: Global Events
 */

symmetry.addNode('core', 'globalEvents', () => {
  const eventPrefix = 'symmetry';
  const ACTIVE_PROJECT_INDEX_CHANGE = 'ACTIVE_PROJECT_INDEX_CHANGE';

  /**
   * @description Dispatch event
   * @param type
   * @param options
   * @returns {*}
   */
  const dispatchEvent = (type, options = {}) => {
    switch ( type ) {
      case ACTIVE_PROJECT_INDEX_CHANGE:
        return document.dispatchEvent(new Event(`${eventPrefix}.component.change-active-project-index`));
      default:
        return false;
    }
  };

  /**
   * @description Intercept event
   * @param type
   * @param options
   * @returns {*}
   */
  const interceptEvent = (type, options = {}) => {
    switch ( type ) {
      case ACTIVE_PROJECT_INDEX_CHANGE:
        return document.addEventListener(`${eventPrefix}.component.change-active-project-index`, options.callback);
      default:
        return false;
    }
  };

  return {
    events: { ACTIVE_PROJECT_INDEX_CHANGE },
    dispatchChangeActiveProjectIndex() {
      dispatchEvent(ACTIVE_PROJECT_INDEX_CHANGE);
      return this;
    },
    interceptChangeActiveProjectIndex(callback) {
      interceptEvent(ACTIVE_PROJECT_INDEX_CHANGE, { callback });
      return this;
    }
  };
});
