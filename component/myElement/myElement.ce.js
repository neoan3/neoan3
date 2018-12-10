let neoan={};


class MyElement extends HTMLElement {

    // A getter/setter for an open property.
    get open() {
        return this.hasAttribute('open');
    }

    set open(val) {
        // Reflect the value of the open property as an HTML attribute.
        if (val) {
            this.setAttribute('open', 'true');
        } else {
            this.removeAttribute('open');
        }
    }



    toggle(){
        !this.open?this.setAttribute('open','true'):this.removeAttribute('open');
        console.log(this.open);

    }


    // Can define constructor arguments if you wish.
    constructor() {
        // If you define a constructor, always call super() first!
        // This is specific to CE and required by the spec.
        super();
        let shadowRoot = this.attachShadow({mode: 'open'});
        shadowRoot.appendChild(document.querySelector('#my-element').content.cloneNode(true));
        // Setup a click listener on <app-drawer> itself.
        this.addEventListener('click', e => {
            // Don't toggle the drawer if it's disabled.
            this.toggle();
        });
        this.addEventListener('apiResponse', e =>{
            console.log(e);
        })
    }

}
window.customElements.define('my-element', MyElement);