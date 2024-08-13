import{k as V,f as m,o as u,l as h,A as t,a as e,w as s,F as N,n as T,p as o,aw as z,c as G,t as p,ax as M,Z as R,ay as H,q as k,az as Z,ad as j,aA as J,aB as K,aC as O,aD as Q,aE as W,aa as X,a6 as Y,aF as ee,aG as oe,a9 as se,aH as ae}from"./7ce62ioI.js";import{_ as L,d as te,t as ne,P as le}from"./BpTLdzrt.js";const re={class:"banner"},ce={class:"banner-inner"},ie={class:"carousel-title"},de={class:"carousel-sub-title px-20 line-clamp-2"},_e=["src"],ue=V({__name:"LoginBanner",setup(w){const a=m(window.AmisAdmin.loginBanner);return(i,g)=>{const d=M,$=z;return u(),h("div",re,[t("div",ce,[e($,{class:"carousel","animation-name":"fade"},{default:s(()=>[(u(!0),h(N,null,T(o(a),l=>(u(),G(d,{key:l.title},{default:s(()=>[(u(),h("div",{key:l.title,class:"carousel-item"},[t("div",ie,p(l.title),1),t("div",de,p(l.desc),1),t("img",{class:"carousel-image",src:l.image},null,8,_e)]))]),_:2},1024))),128))]),_:1})])])}}}),me=L(ue,[["__scopeId","data-v-0566b7fc"]]),pe=(w=!1)=>{const a=m(w);return{loading:a,setLoading:d=>{a.value=d},toggle:()=>{a.value=!a.value}}},ge={class:"login-form-wrapper"},fe={class:"login-form-title"},ve={class:"login-form-sub-title"},he={class:"login-form-error-msg"},we=["src"],be={class:"login-form-password-actions"},xe=V({__name:"LoginForm",setup(w){const a=m(window.AmisAdmin),i=m(""),{loading:g,setLoading:d}=pe(),$=te(),l=m(a.value.captchaUrl),y=()=>{l.value=`${a.value.captchaUrl}?t=${Date.now()}`},r=R("login-config",{rememberPassword:!0,username:"",password:""}),c=H({username:r.value.username,password:r.value.password,verification_code:""}),C=async({errors:b,values:n})=>{if(i.value="",!g.value&&!b){d(!0);try{await $.login(n),Z.success("登录成功"),ne({name:le.home});const{rememberPassword:_}=r.value,{username:x,password:f}=n;r.value.username=_?x:"",r.value.password=_?f:""}catch(_){i.value=_.message}finally{d(!1)}}},A=b=>{r.value.rememberPassword=b};return(b,n)=>{const _=j,x=J,f=K,B=O,S=Q,P=W,F=X,I=Y,U=ee,q=oe,D=se,E=ae;return u(),h("div",ge,[t("div",fe,p(o(a).loginTitle),1),t("div",ve,p(o(a).loginDesc),1),t("div",he,p(o(i)),1),e(E,{ref:"loginForm",model:o(c),class:"login-form",layout:"vertical",onSubmit:C},{default:s(()=>[e(f,{field:"username",rules:[{required:!0,message:"用户名不能为空"}],"validate-trigger":["change","blur"],"hide-label":""},{default:s(()=>[e(x,{modelValue:o(c).username,"onUpdate:modelValue":n[0]||(n[0]=v=>o(c).username=v),placeholder:"用户名"},{prefix:s(()=>[e(_)]),_:1},8,["modelValue"])]),_:1}),e(f,{field:"password",rules:[{required:!0,message:"密码不能为空"}],"validate-trigger":["change","blur"],"hide-label":""},{default:s(()=>[e(S,{modelValue:o(c).password,"onUpdate:modelValue":n[1]||(n[1]=v=>o(c).password=v),placeholder:"密码","allow-clear":""},{prefix:s(()=>[e(B)]),_:1},8,["modelValue"])]),_:1}),e(f,{field:"verification_code",rules:[{required:!0,message:" 验证码不能为空"}],"validate-trigger":["change","blur"],"hide-label":""},{default:s(()=>[e(I,null,{default:s(()=>[e(x,{modelValue:o(c).verification_code,"onUpdate:modelValue":n[2]||(n[2]=v=>o(c).verification_code=v),placeholder:"验证码"},{prefix:s(()=>[e(P)]),_:1},8,["modelValue"]),e(F,{content:"点击刷新验证码"},{default:s(()=>[t("img",{src:o(l),onClick:y,alt:"",srcset:"",class:"w-full h-32px rounded-2px cursor-pointer"},null,8,we)]),_:1})]),_:1})]),_:1}),e(I,{size:16,direction:"vertical"},{default:s(()=>[t("div",be,[e(U,{checked:"rememberPassword","model-value":o(r).rememberPassword,onChange:A},{default:s(()=>[k("记住密码")]),_:1},8,["model-value","onChange"]),e(q,null,{default:s(()=>[k("忘记密码")]),_:1})]),e(D,{type:"primary","html-type":"submit",long:"",loading:o(g)},{default:s(()=>[k(" 登录 ")]),_:1},8,["loading"])]),_:1})]),_:1},8,["model"])])}}}),$e=L(xe,[["__scopeId","data-v-bffd17af"]]),ke={class:"container"},Ve={class:"logo"},Le=["src"],Ie={class:"logo-text"},ye={class:"content"},Ce={class:"content-inner"},Ae=V({__name:"LoginView",setup(w){const a=m(window.AmisAdmin);return localStorage.setItem("arco-theme","light"),(i,g)=>(u(),h("div",ke,[t("div",Ve,[t("img",{alt:"logo",class:"h-33 w-33",src:o(a).logo},null,8,Le),t("div",Ie,p(o(a).title),1)]),e(me),t("div",ye,[t("div",Ce,[e($e)])])]))}}),Pe=L(Ae,[["__scopeId","data-v-f1e1325c"]]);export{Pe as default};
