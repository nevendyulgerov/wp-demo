/* globals symmetry, ammo */

/**
 * Core: Api
 */

symmetry.addNode('core', 'api', () => {

  const { ajaxUrl, ajaxAction, ajaxToken } = window.symmetrySettings;
  const $ = window.jQuery;

  /**
   * @description Ajax request
   * @param options
   */
  const ajax = (options) => {
    const ajaxOptions = getAjaxOptions(options);
    $.ajax(ajaxOptions);
  };

  /**
   * @description Validate response
   * @param err
   * @param res
   * @returns {*}
   */
  const validateResponse = (err, res) => {
    let errorMessage = `Ajax error. Unable to fetch data. Error message is: {error}`;
    let hasError = false;

    if (err) {
      errorMessage = errorMessage.replace('{error}', err.responseText);
      hasError = true;
    } else if (res && res.status === 'error') {
      errorMessage = errorMessage.replace('{error}', res.message);
      hasError = true;
    }

    if (hasError) {
      console.error(errorMessage);
      return false;
    }
    return true;
  };

  /**
   * @description Get ajax options
   * @param method
   * @param data
   * @param callback
   * @returns object
   */
  const getAjaxOptions = ({ method, data, callback }) => {
    return {
      data: {
        action: `${ajaxAction}`,
        data: data || {},
        method,
        token: ajaxToken,
      },
      dataType: 'JSON',
      error: error => callback(error),
      success: response => callback(undefined, response),
      type: 'POST',
      url: ajaxUrl,
    };
  };

  /**
   * @description Get facilities
   * @param callback
   */
  const getInterfaceSettings = ({ callback }) => {
    ajax({
      method: 'get_interface_settings',
      callback
    });
  };

  /**
   * @description Get projects
   * @param query
   * @param callback
   */
  const getProjects = ({ query, callback }) => {
    ajax({
      method: 'get_projects',
      data: query,
      callback
    });
  };

  return {
    getProjects,
    getInterfaceSettings,
    validateResponse,
  };
});
