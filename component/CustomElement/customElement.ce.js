export class CustomElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.shadowRoot.innerHTML = 'Loading...';
        this.templating()
    }
    async templating(){
        let result = await fetch('{{base}}api.v1/custom-element')
            .then(j => j.json())
        this.render(result)
    }
    render(data){
        this.shadowRoot.innerHTML = data.tip;
    }
}
customElements.define('custom-element', CustomElement)