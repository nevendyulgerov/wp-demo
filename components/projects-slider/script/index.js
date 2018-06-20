/* globals symmetry, ammo, jQuery */

symmetry.addNode('components', 'projects-slider', ($component) => {
  const { isObj, selectAll, app, buffer, scrollSpy, poll } = ammo;
  const { dispatchChangeActiveProjectIndex, interceptChangeActiveProjectIndex } = symmetry.getNode('core', 'globalEvents')();
  const { getProjects } = symmetry.getNode('core', 'api')();
  const $ = jQuery;

  const props = {
    strongTypes: true,
    storeKey: 'projects-slider'
  };
  const state = {
    projects: { type: 'array', value: [] },
    offsets: { type: 'array', value: [] },
    activeProjectIndex: { type: 'number', value: 0 },
    isMovingToNextProject: { type: 'boolean', value: false },
    isLoadingProjects: { type: 'boolean', value: false },
    projectsPerPage: { type: 'number', value: 5 },
    projectsOffsetIndex: { type: 'number', value: 2 }
  };
  const component = app(props, state).schema('component').syncWithPersistentStore();

  component.configure('events')
    .node('onGoToNextProject', callback => {
      ammo.delegateEvent('click', '.trigger.go-to-next-project', callback, $component);
    });

  component.configure('renderers')
    .node('animateMoveToNextProject', (scrollTop, duration = 2000, easing = 'easeInOutExpo') => {
      $('html, body').animate({ scrollTop }, duration, easing, () => {
        component.updateStore('isMovingToNextProject', () => false);
      });
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
    .node('randomGradient', ($target, multiplier) => {
      const randomGradient = () => {
        const c1 = {
          r: Math.floor(Math.random() * multiplier),
          g: Math.floor(Math.random() * multiplier),
          b: Math.floor(Math.random() * multiplier)
        };
        const c2 = {
          r: Math.floor(Math.random() * multiplier),
          g: Math.floor(Math.random() * multiplier),
          b: Math.floor(Math.random() * multiplier)
        };
        c1.rgb = 'rgb(' + c1.r + ',' + c1.g + ',' + c1.b + ')';
        c2.rgb = 'rgb(' + c2.r + ',' + c2.g + ',' + c2.b + ')';
        return 'radial-gradient(at top left, ' + c1.rgb + ', ' + c2.rgb + ')';
      };
      $target.css({'background': randomGradient()});
    })
    .node('getMoreProjects', () => {
      component.updateStore('isLoadingProjects', () => true);
      const { projects, projectsPerPage, projectsOffsetIndex } = component.getStore();

      getProjects({
        query: {
          projectsOffsetIndex: projectsOffsetIndex,
          projectsPerPage: projectsPerPage
        },
        callback: (err, res) => {
          if (err) {
            return console.log(err);
          }
          const { data } = res;
          component.updateStore('isLoadingProjects', () => false);
          component.updateStore('projectsPageIndex', projectsPageIndex => projectsPageIndex + 1);
          component.updateStore('projects', () => [...projects, ...data]);
          console.log('next projects:');
          console.log(data);
        }
      });
    })
    .node('getActiveProjectIndex', (questionOffsets, scrollVerticalOffset) => {
      const baseOffset = 300;
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

      selectAll('.slider-item', $component).each(project => {
        actions.randomGradient($(project.querySelector('.overlay')), 320);
      });

      interceptChangeActiveProjectIndex(() => {
        const { activeProjectIndex } = component.getStore();
        const projects = selectAll('.slider-item', $component);
        const activeProject = projects.eq(activeProjectIndex);

        projects.each((project, index) => {
          if (index === activeProjectIndex || !project.classList.contains(activeFlag)) {
            return false;
          }

          project.classList.remove(activeFlag);
          const projectTeamMembers = selectAll('.team-member-item', project);

          projectTeamMembers.async((resolve, teamMember) => {
            teamMember.classList.remove(activeFlag);
            setTimeout(() => resolve(), 100);
          });
        });

        activeProject.classList.add(activeFlag);
        const activeProjectTeamMembers = selectAll('.team-member-item', activeProject);

        activeProjectTeamMembers.async((resolve, teamMember) => {
          teamMember.classList.add(activeFlag);
          setTimeout(() => resolve(), 100);
        });
      });

      const bufferScrollBefore = buffer();
      const bufferScrollAfter = buffer();

      poll(resolve => {
        const { activeProjectIndex } = component.getStore();
        const activeProject = selectAll('.slider-item', $component).eq(activeProjectIndex);

        activeProject.querySelector('.trigger.go-to-next-project').classList.add(activeFlag);

        setTimeout(() => {
          activeProject.querySelector('.trigger.go-to-next-project').classList.remove(activeFlag);
          resolve(true);
        }, 3000);
      }, () => {}, 3000);

      scrollSpy({
        offset: 30,
        initOnLoad: true,
        callbacks: {
          onBefore: verticalScroll => {
            const { offsets } = component.getStore();
            if (header.classList.contains(shrinkFlag)) {
              header.classList.remove(shrinkFlag)
            }

            const nextActiveProjectIndex = actions.getActiveProjectIndex(offsets, verticalScroll);

            bufferScrollBefore('buffered-scroll-before', 100, () => {
              const { isLoadingProjects } = component.getStore();
              const isLastIndex = nextActiveProjectIndex === offsets.length - 1;
              component.updateStore('activeProjectIndex', () => nextActiveProjectIndex);
              dispatchChangeActiveProjectIndex();

              if (isLastIndex && !isLoadingProjects) {
                actions.getMoreProjects();
              }
            });
          },
          onAfter: verticalScroll => {
            if (!header.classList.contains(shrinkFlag)) {
              header.classList.add(shrinkFlag)
            }

            const nextActiveProjectIndex = actions.getActiveProjectIndex(offsets, verticalScroll);

            component.updateStore('activeProjectIndex', () => nextActiveProjectIndex);
            bufferScrollAfter('buffered-scroll-after', 100, () => {
              const { isLoadingProjects } = component.getStore();
              const isLastIndex = nextActiveProjectIndex === offsets.length - 1;
              dispatchChangeActiveProjectIndex();

              if (isLastIndex && !isLoadingProjects) {
                actions.getMoreProjects();
              }
            });
          }
        }
      });

      events.onGoToNextProject(() => {
        component.updateStore('isMovingToNextProject', () => true);
        renderers.goToNextProject();
      });
    });

  component.callNode('actions', 'init');
});