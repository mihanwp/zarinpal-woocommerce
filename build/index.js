(()=>{const{registerPaymentMethod:t}=window.wc.wcBlocksRegistry,{createElement:e,useState:n,useEffect:a,useRef:i}=window.wp.element,r=window.wc.wcSettings.getSetting("paymentMethodData")?.zarinpal,s=wcZarinpalSettings?.gateName||"zarinpal",c=t=>{const{eventRegistration:r={},emitResponse:c}=t,[o,p]=n(!1),l=i(!1);return a((()=>{if(!r.onPaymentProcessing)return;const t=()=>{l.current=!1,p(!1)},e=r.onCheckoutValidation?.((()=>{t()})),n=r.onCheckoutFail?.((()=>{t()})),a=r.onPaymentSetup((()=>{l.current||(l.current=!0,p(!0));try{return{type:"success",meta:{paymentMethodData:{payment_method:s,title:wcZarinpalSettings?.title,description:wcZarinpalSettings?.description}}}}catch(e){return t(),{type:"error",message:e.message}}}));return()=>{t(),"function"==typeof a&&a(),"function"==typeof e&&e(),"function"==typeof n&&n()}}),[r]),a((()=>()=>{l.current=!1,p(!1)}),[]),o&&l.current,e("div",{className:"wc-zarinpal-payment-method"},e("div",{className:"wc-zarinpal-description"},wcZarinpalSettings?.description||"پرداخت امن به وسیله درگاه زرین‌پال"),e("div",{className:"wc-zarinpal-logo"},e("img",{src:wcZarinpalSettings?.logoUrl,alt:"ZarinPal",style:{height:"24px",marginTop:"10px"}})))};t({name:s,label:r?.title||"پرداخت امن زرین‌پال",content:e(c),edit:e((t=>e(c,{...t,isEdit:!0}))),canMakePayment:()=>!0,ariaLabel:r?.title||"پرداخت با زرین‌پال",supports:{features:r?.supports||["products"]}})})();