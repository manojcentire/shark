(window.webpackJsonp=window.webpackJsonp||[]).push([[1],{41:function(e,t,n){"use strict";n.d(t,"a",(function(){return l}));var a=n(53),c=n(0),r=n.n(c);function l(e){var t=e.title,n=e.children,l=Object(a.usePage)().flash;return Object(c.useEffect)((function(){document.title=t}),[t]),Object(c.useEffect)((function(){(l.error||l.success)&&window.Events.$emit("flash",l.error||l.success)})),r.a.createElement(r.a.Fragment,null,n)}},84:function(e,t,n){"use strict";n.r(t);var a=n(0),c=n.n(a),r=n(168),l=n(170),o=n(169),i=n(62),s=n(36),u={SkeletonBodyTextContainer:"Polaris-SkeletonBodyText__SkeletonBodyTextContainer",SkeletonBodyText:"Polaris-SkeletonBodyText"};function m({lines:e=3}){for(var t=[],n=0;n<e;n++)t.push(c.a.createElement("div",{className:u.SkeletonBodyText,key:n}));return c.a.createElement("div",{className:u.SkeletonBodyTextContainer},t)}var d=n(21),E=n(7),f=n(41);t.default=function(e){return c.a.createElement(f.a,{title:"Home"},c.a.createElement(r.a,{title:"Home",secondaryActions:[{content:"Home",disabled:Object(E.b)().current("home"),onAction:function(){return e.beforeSend().then((function(e){var t=e.headers;d.Inertia.visit(Object(E.b)("home"),{headers:t})}))}},{content:"Settings",disabled:Object(E.b)().current("setting.index"),onAction:function(){return e.beforeSend().then((function(e){var t=e.headers;d.Inertia.visit(Object(E.b)("setting.index"),{headers:t})}))}}],actionGroups:[]},c.a.createElement(l.a,null,c.a.createElement(l.a.Section,null,c.a.createElement(o.a,{status:"info",title:"You're logged in as ".concat(Object(E.a)("shop.shopify_domain"))})),c.a.createElement(l.a.Section,null,c.a.createElement(i.a,{sectioned:!0},c.a.createElement(s.a,{size:"extraLarge"},"What a beautiful day!")),c.a.createElement(i.a,{sectioned:!0,title:"Section name"},c.a.createElement(m,null)),c.a.createElement(i.a,{sectioned:!0,title:"Section name"},c.a.createElement(m,null))),c.a.createElement(l.a.Section,{secondary:!0},c.a.createElement(i.a,{title:"Section name"},c.a.createElement(i.a.Section,null,c.a.createElement(m,{lines:2})),c.a.createElement(i.a.Section,null,c.a.createElement(m,{lines:1}))),c.a.createElement(i.a,{title:"Section name",subdued:!0},c.a.createElement(i.a.Section,null,c.a.createElement(m,{lines:2})),c.a.createElement(i.a.Section,null,c.a.createElement(m,{lines:2})))))))}}}]);