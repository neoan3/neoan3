class NeoanApi extends HTMLElement {
    // getter
    get target() {
        return this.hasAttribute('target');
    }
    get targetValue(){
        return this.getAttribute('target');
    }
    get global(){
        return this.hasAttribute('global');
    }

    get payload(){
        return this.hasAttribute('payload');
    }
    get payloadValue(){
        return this.getAttribute('payload');
    }
    // setter
    set target(url){
        if (url) {
            this.setAttribute('target', url);
        } else {
            this.removeAttribute('target');
        }
    }
    set payload(data){
        if (data) {
            this.setAttribute('payload', data);
        } else {
            this.removeAttribute('payload');
        }
    }

    attachResult(response){

        this.dispatchEvent(new CustomEvent('apiResponse',{
            bubbles:this.global,
            detail:response
        }));
        if(this.firstChild){
            this.firstChild.setAttribute('neoan-api-response',JSON.stringify(response))
        }

    }
    send(){
        const http = new XMLHttpRequest();
        let  out = this;
        let parts = out.targetValue.split('::');
        let obj = {
            c:parts[0],
            f:parts[1],
            d:out.payloadValue,
            config:'kit'
        };
        http.open('post','/neoan3/_neoan/base/Api.php',true);
        http.setRequestHeader('Content-type', 'application/json');
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                out.attachResult(JSON.parse(http.responseText));
            }
        };
        http.send(JSON.stringify(obj));
    }
    constructor() {
        super();

        if(this.target){
            this.send();
        }

    }
}
window.customElements.define('neoan-api', NeoanApi);