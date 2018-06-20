/* globals symmetry, ammo, jQuery */

symmetry.addNode('components', 'projects-slider', ($component) => {
  const { isObj, selectAll, app, buffer } = ammo;
  const { dispatchChangeActiveProjectIndex, interceptChangeActiveProjectIndex } = symmetry.getNode('core', 'globalEvents')();
  const $ = jQuery;

  const props = {
    strongTypes: true
  };
  const state = {
    offsets: { type: 'array', value: [] },
    activeProjectIndex: { type: 'number', value: 0 }
  };
  const component = app(props, state).schema('component');

  component.configure('events')
    .node('onGoToNextProject', callback => {
      ammo.delegateEvent('click', '.trigger.go-to-next-project', callback, $component);
    });

  component.configure('renderers')
    .node('animateMoveToNextProject', (scrollTop, duration = 2000, easing = 'easeInOutExpo') => {
      $('html, body').animate({ scrollTop }, duration, easing);
    })
    .node('goToNextProject', () => {
      const { animateMoveToNextProject } = component.getNodes('renderers');
      const activeFlag = 'active';
      const projects = selectAll('.slider-item', $component);
      const activeProject = $component.querySelector('.slider-item.active');
      let activeProjectIndex = -1;
      let nextActiveProject = '';

      activeProject.classList.remove(activeFlag);

      projects.each((project, index) => {
        if (isObj(nextActiveProject)) {
          return false;
        }
        if (index - 1 >= 0 && projects.eq(index - 1).isSameNode(activeProject)) {
          activeProjectIndex = index;
          nextActiveProject = project;
        }
      });

      if (!isObj(nextActiveProject)) {
        return false;
      }

      nextActiveProject.classList.add(activeFlag);

      const $nextActiveProject = $(nextActiveProject);
      const nextActiveProjectOffset = $nextActiveProject.offset().top;
      const scrollTop = activeProjectIndex > 0 ? nextActiveProjectOffset : nextActiveProjectOffset;
      animateMoveToNextProject(scrollTop);
    });

  component.configure('actions')
    .node('getActiveProjectIndex', (questionOffsets, scrollVerticalOffset) => {
      const baseOffset = 60;
      let nextActiveQuestionIndex = -1;

      questionOffsets.forEach((questionOffset, index) => {
        if (nextActiveQuestionIndex > -1) {
          return false;
        }
        if (index + 1 < questionOffsets.length) {
          const nextQuestionOffset = questionOffsets[index + 1];
          if (questionOffset - baseOffset >= scrollVerticalOffset && scrollVerticalOffset < nextQuestionOffset - baseOffset) {
            nextActiveQuestionIndex = index;
          }
        } else {
          nextActiveQuestionIndex = index;
        }
      });

      return nextActiveQuestionIndex;
    })
    .node('getOffsets', () => {
      const offsets = [];
      const projects = selectAll('.slider-item', $component);
      projects.each(project => {
        const $project = $(project);
        const offset = parseInt($project.offset().top + $project.height(), 10);
        offsets.push(offset);
      });
      return offsets;
    })
    .node('init', () => {
      const { events, renderers, actions } = component.getNodes();
      const header = document.querySelector('.site-header');
      const activeFlag = 'active';
      const shrinkFlag = 'shrunk';
      const offsets = actions.getOffsets();
      component.updateStore('offsets', () => offsets);

      interceptChangeActiveProjectIndex(() => {
        const { activeProjectIndex } = component.getStore();
        const projects = selectAll('.slider-item', $component);
        const activeProject = projects.eq(activeProjectIndex);

        projects.each((project, index) => {
          if (index === activeProjectIndex || !project.classList.contains(activeFlag)) {
            return false;
          }
          project.classList.remove(activeFlag);
        });
        activeProject.classList.add(activeFlag);
      });

      const bufferScrollBefore = buffer();
      const bufferScrollAfter = buffer();

      ammo.scrollSpy({
        offset: 100,
        initOnLoad: true,
        callbacks: {
          onBefore: verticalScroll => {
            const { offsets } = component.getStore();
            if (header.classList.contains(shrinkFlag)) {
              header.classList.remove(shrinkFlag)
            }

            const nextActiveProjectIndex = actions.getActiveProjectIndex(offsets, verticalScroll);
            component.updateStore('activeProjectIndex', () => nextActiveProjectIndex);
            bufferScrollBefore('buffered-scroll-before', 100, () => dispatchChangeActiveProjectIndex());
          },
          onAfter: verticalScroll => {
            if (!header.classList.contains(shrinkFlag)) {
              header.classList.add(shrinkFlag)
            }

            const nextActiveProjectIndex = actions.getActiveProjectIndex(offsets, verticalScroll);
            component.updateStore('activeProjectIndex', () => nextActiveProjectIndex);
            bufferScrollAfter('buffered-scroll-after', 100, () => dispatchChangeActiveProjectIndex());
          }
        }
      });

      events.onGoToNextProject(renderers.goToNextProject);
    });

  component.callNode('actions', 'init');
});