import { u as useApi, _ as __nuxt_component_2 } from './useApi-DLNjmAdL.mjs';
import { _ as __nuxt_component_1, a as __nuxt_component_2$1 } from './CourseTable-DJQPWFyA.mjs';
import { defineComponent, ref, computed, unref, withCtx, createVNode, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrRenderComponent } from 'vue/server-renderer';
import './server.mjs';
import '../nitro/nitro.mjs';
import 'node:http';
import 'node:https';
import 'node:events';
import 'node:buffer';
import 'node:fs';
import 'node:path';
import 'node:crypto';
import 'node:url';
import '@iconify/utils';
import 'consola';
import 'vue-router';
import 'perfect-debounce';
import '../routes/renderer.mjs';
import 'vue-bundle-renderer/runtime';
import 'unhead/server';
import 'devalue';
import 'unhead/utils';

const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "schedule",
  __ssrInlineRender: true,
  setup(__props) {
    useApi();
    const loading = ref(true);
    const classes = ref([]);
    const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    const schedule = computed(() => {
      const grid = {};
      for (const day of days) {
        grid[day] = [];
      }
      for (const cls of classes.value) {
        if (cls.sessions) {
          for (const session of cls.sessions) {
            if (grid[session.day]) {
              grid[session.day].push({
                ...session,
                course_code: cls.course_code ?? cls.code,
                course_name: cls.course_name ?? cls.name,
                section: cls.section,
                class_id: cls.id
              });
            }
          }
        }
      }
      for (const day of days) {
        grid[day].sort((a, b) => a.start_at.localeCompare(b.start_at));
      }
      return grid;
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_LoadingState = __nuxt_component_2;
      const _component_WeeklySchedule = __nuxt_component_1;
      const _component_CourseTable = __nuxt_component_2$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_LoadingState, { loading: unref(loading) }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_WeeklySchedule, {
              title: "Instructor Schedule",
              schedule: unref(schedule),
              days,
              linkable: "",
              "link-base-path": "/instructor/class"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-8"${_scopeId}><h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4"${_scopeId}> Assigned Courses </h2><div class="card overflow-hidden p-0"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_CourseTable, {
              classes: unref(classes),
              "link-base-path": "/instructor/class"
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode(_component_WeeklySchedule, {
                title: "Instructor Schedule",
                schedule: unref(schedule),
                days,
                linkable: "",
                "link-base-path": "/instructor/class"
              }, null, 8, ["schedule"]),
              createVNode("div", { class: "mt-8" }, [
                createVNode("h2", { class: "text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4" }, " Assigned Courses "),
                createVNode("div", { class: "card overflow-hidden p-0" }, [
                  createVNode(_component_CourseTable, {
                    classes: unref(classes),
                    "link-base-path": "/instructor/class"
                  }, null, 8, ["classes"])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/instructor/schedule.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=schedule-CNpeQpqV.mjs.map
