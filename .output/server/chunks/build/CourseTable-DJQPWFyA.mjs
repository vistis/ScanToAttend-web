import { defineComponent, computed, mergeProps, unref, createVNode, resolveDynamicComponent, resolveComponent, withCtx, toDisplayString, createTextVNode, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrInterpolate, ssrRenderStyle, ssrRenderList, ssrRenderVNode } from 'vue/server-renderer';

const startHour = 7;
const endHour = 19;
const hourHeight = 64;
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "WeeklySchedule",
  __ssrInlineRender: true,
  props: {
    title: {},
    schedule: {},
    days: {},
    linkable: { type: Boolean },
    linkBasePath: {}
  },
  setup(__props) {
    const props = __props;
    const dayLabels = {
      Monday: "MON",
      Tuesday: "TUE",
      Wednesday: "WED",
      Thursday: "THU",
      Friday: "FRI",
      Saturday: "SAT",
      Sunday: "SUN"
    };
    const hours = computed(() => {
      const h = [];
      for (let i = startHour; i <= endHour; i++) {
        h.push(i);
      }
      return h;
    });
    function formatHour(hour) {
      if (hour === 0) return "12 AM";
      if (hour === 12) return "12 PM";
      return `${hour > 12 ? hour - 12 : hour} ${hour >= 12 ? "PM" : "AM"}`;
    }
    function timeToHours(time) {
      const parts = time.split(":");
      return Number.parseInt(parts[0]) + Number.parseInt(parts[1]) / 60;
    }
    function timeToTop(time) {
      return (timeToHours(time) - startHour) * hourHeight;
    }
    function sessionHeight(start, end) {
      return (timeToHours(end) - timeToHours(start)) * hourHeight;
    }
    const courseColors = [
      { bg: "#2563eb", border: "#1d4ed8" },
      // blue
      { bg: "#dc2626", border: "#b91c1c" },
      // red
      { bg: "#0891b2", border: "#0e7490" },
      // cyan
      { bg: "#e11d48", border: "#be123c" },
      // rose
      { bg: "#0d9488", border: "#0f766e" },
      // teal
      { bg: "#7c3aed", border: "#6d28d9" },
      // violet
      { bg: "#ea580c", border: "#c2410c" },
      // orange
      { bg: "#059669", border: "#047857" },
      // emerald
      { bg: "#4f46e5", border: "#4338ca" },
      // indigo
      { bg: "#be185d", border: "#9d174d" }
      // pink
    ];
    const courseColorMap = computed(() => {
      const map = {};
      const codes = /* @__PURE__ */ new Set();
      for (const day of props.days) {
        for (const session of props.schedule[day] ?? []) {
          codes.add(session.course_code);
        }
      }
      let i = 0;
      for (const code of codes) {
        map[code] = courseColors[i % courseColors.length];
        i++;
      }
      return map;
    });
    function getColor(courseCode) {
      return courseColorMap.value[courseCode] ?? courseColors[0];
    }
    function layoutSessions(sessions) {
      if (!sessions.length) return [];
      const sorted = [...sessions].sort((a, b) => timeToHours(a.start_at) - timeToHours(b.start_at));
      const result = [];
      const groups = [];
      let currentGroup = [sorted[0]];
      let currentEnd = timeToHours(sorted[0].end_at);
      for (let i = 1; i < sorted.length; i++) {
        if (timeToHours(sorted[i].start_at) < currentEnd) {
          currentGroup.push(sorted[i]);
          currentEnd = Math.max(currentEnd, timeToHours(sorted[i].end_at));
        } else {
          groups.push(currentGroup);
          currentGroup = [sorted[i]];
          currentEnd = timeToHours(sorted[i].end_at);
        }
      }
      groups.push(currentGroup);
      for (const group of groups) {
        const totalCols = group.length;
        for (let col = 0; col < group.length; col++) {
          result.push({ ...group[col], col, totalCols });
        }
      }
      return result;
    }
    const gridHeight = computed(() => (endHour - startHour) * hourHeight);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full" }, _attrs))}><div class="text-center mb-2"><h2 class="text-lg font-bold tracking-widest text-slate-700 dark:text-slate-300 uppercase">${ssrInterpolate(__props.title)}</h2></div><div class="h-1 rounded-full bg-gradient-to-r from-teal-400 via-cyan-400 to-blue-500 mb-5"></div><div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-[#1a1f2e] overflow-x-auto"><div class="min-w-[820px]"><div class="grid bg-slate-50 dark:bg-[#151926] border-b border-slate-200 dark:border-slate-700/60" style="${ssrRenderStyle({ gridTemplateColumns: `56px repeat(${__props.days.length}, 1fr)` })}"><div></div><!--[-->`);
      ssrRenderList(__props.days, (day) => {
        _push(`<div class="py-3 text-center text-xs font-bold tracking-widest text-slate-500 dark:text-slate-400 border-l border-slate-200 dark:border-slate-700/60 select-none">${ssrInterpolate(dayLabels[day] || day.slice(0, 3).toUpperCase())}</div>`);
      });
      _push(`<!--]--></div><div class="relative grid" style="${ssrRenderStyle({ gridTemplateColumns: `56px repeat(${__props.days.length}, 1fr)` })}"><div class="relative" style="${ssrRenderStyle({ height: `${unref(gridHeight)}px` })}"><!--[-->`);
      ssrRenderList(unref(hours), (hour) => {
        _push(`<div class="absolute right-0 pr-2 text-[11px] font-semibold text-slate-400 dark:text-slate-500 -translate-y-1/2 select-none" style="${ssrRenderStyle({ top: `${(hour - startHour) * hourHeight}px` })}">${ssrInterpolate(formatHour(hour))}</div>`);
      });
      _push(`<!--]--></div><!--[-->`);
      ssrRenderList(__props.days, (day) => {
        _push(`<div class="relative border-l border-slate-200 dark:border-slate-700/60" style="${ssrRenderStyle({ height: `${unref(gridHeight)}px` })}"><!--[-->`);
        ssrRenderList(unref(hours), (hour) => {
          _push(`<div class="absolute w-full border-t border-slate-100 dark:border-slate-700/30" style="${ssrRenderStyle({ top: `${(hour - startHour) * hourHeight}px` })}"></div>`);
        });
        _push(`<!--]--><!--[-->`);
        ssrRenderList(layoutSessions(__props.schedule[day] ?? []), (session) => {
          ssrRenderVNode(_push, createVNode(resolveDynamicComponent(__props.linkable ? ("resolveComponent" in _ctx ? _ctx.resolveComponent : unref(resolveComponent))("NuxtLink") : "div"), mergeProps({ ref_for: true }, __props.linkable ? { to: `${__props.linkBasePath}/${session.class_id}` } : {}, {
            class: ["absolute z-10 mx-[3px] rounded-lg overflow-hidden flex flex-col justify-center transition-all duration-150", [__props.linkable ? "hover:brightness-110 hover:shadow-lg cursor-pointer" : "cursor-default"]],
            style: {
              top: `${timeToTop(session.start_at) + 1}px`,
              height: `${sessionHeight(session.start_at, session.end_at) - 2}px`,
              backgroundColor: getColor(session.course_code).bg,
              borderLeft: `3px solid ${getColor(session.course_code).border}`,
              left: session.totalCols > 1 ? `${session.col / session.totalCols * 100}%` : "0",
              right: session.totalCols > 1 ? `${(session.totalCols - session.col - 1) / session.totalCols * 100}%` : "0"
            }
          }), {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<div class="flex flex-col items-center justify-center flex-1 px-1.5 py-1 text-white text-center min-h-0"${_scopeId}><p class="font-bold text-sm leading-tight truncate w-full"${_scopeId}>${ssrInterpolate(session.course_code)}</p><p class="text-[11px] leading-tight opacity-90 truncate w-full mt-0.5"${_scopeId}>${ssrInterpolate(session.start_at?.slice(0, 5))} - ${ssrInterpolate(session.end_at?.slice(0, 5))}</p></div>`);
              } else {
                return [
                  createVNode("div", { class: "flex flex-col items-center justify-center flex-1 px-1.5 py-1 text-white text-center min-h-0" }, [
                    createVNode("p", { class: "font-bold text-sm leading-tight truncate w-full" }, toDisplayString(session.course_code), 1),
                    createVNode("p", { class: "text-[11px] leading-tight opacity-90 truncate w-full mt-0.5" }, toDisplayString(session.start_at?.slice(0, 5)) + " - " + toDisplayString(session.end_at?.slice(0, 5)), 1)
                  ])
                ];
              }
            }),
            _: 2
          }), _parent);
        });
        _push(`<!--]--></div>`);
      });
      _push(`<!--]--></div></div></div></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/WeeklySchedule.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = Object.assign(_sfc_main$1, { __name: "WeeklySchedule" });
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "CourseTable",
  __ssrInlineRender: true,
  props: {
    classes: {},
    showInstructor: { type: Boolean },
    linkBasePath: {}
  },
  setup(__props) {
    function dayAbbrev(day) {
      const map = {
        Monday: "Mon",
        Tuesday: "Tue",
        Wednesday: "Wed",
        Thursday: "Thu",
        Friday: "Fri",
        Saturday: "Sat",
        Sunday: "Sun"
      };
      return map[day] ?? day.slice(0, 3);
    }
    function formatSession(session) {
      const day = dayAbbrev(session.day);
      const start = session.start_at?.slice(0, 5) ?? "";
      const end = session.end_at?.slice(0, 5) ?? "";
      return `(${day}) ${start} - ${end}`;
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "overflow-x-auto" }, _attrs))}><table class="w-full text-sm text-left"><thead><tr class="border-b border-slate-100 dark:border-slate-700"><th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider"> Course Code </th><th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider"> Course Title </th>`);
      if (__props.showInstructor) {
        _push(`<th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider"> Instructor </th>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider"> Schedule </th></tr></thead><tbody><!--[-->`);
      ssrRenderList(__props.classes, (cls) => {
        _push(`<tr class="border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"><td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white whitespace-nowrap">`);
        ssrRenderVNode(_push, createVNode(resolveDynamicComponent(__props.linkBasePath ? ("resolveComponent" in _ctx ? _ctx.resolveComponent : unref(resolveComponent))("NuxtLink") : "span"), mergeProps({ ref_for: true }, __props.linkBasePath ? { to: `${__props.linkBasePath}/${cls.id}`, class: "text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:underline decoration-primary-300 dark:decoration-primary-600 underline-offset-2" } : {}), {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(cls.course_code ?? cls.code)} Section ${ssrInterpolate(cls.section)}`);
            } else {
              return [
                createTextVNode(toDisplayString(cls.course_code ?? cls.code) + " Section " + toDisplayString(cls.section), 1)
              ];
            }
          }),
          _: 2
        }), _parent);
        _push(`</td><td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">${ssrInterpolate(cls.course_name ?? cls.name)}</td>`);
        if (__props.showInstructor) {
          _push(`<td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">`);
          if (cls.instructor_first_name || cls.instructor_last_name) {
            _push(`<!--[-->${ssrInterpolate(cls.instructor_first_name)} ${ssrInterpolate(cls.instructor_last_name)}<!--]-->`);
          } else {
            _push(`<span class="text-slate-300 dark:text-slate-600">—</span>`);
          }
          _push(`</td>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">`);
        if (cls.sessions && cls.sessions.length) {
          _push(`<div class="space-y-0.5"><!--[-->`);
          ssrRenderList(cls.sessions, (session) => {
            _push(`<div class="text-xs font-medium">${ssrInterpolate(formatSession(session))}</div>`);
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<span class="text-slate-300 dark:text-slate-600 text-xs">No schedule</span>`);
        }
        _push(`</td></tr>`);
      });
      _push(`<!--]--></tbody></table></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/CourseTable.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __nuxt_component_2 = Object.assign(_sfc_main, { __name: "CourseTable" });

export { __nuxt_component_1 as _, __nuxt_component_2 as a };
//# sourceMappingURL=CourseTable-DJQPWFyA.mjs.map
