const apiEndpoint = `{{endpoint}}`;
const result = document.getElementById('result')
function endpointCtrl(){
    return {
        data: {
            authorization:'',
            method:'GET',
            endpoint:'endpoint',
            payload:'',
            response:'',
            activeTab: 1,
            statusClass: 'text-primary'
        },
        send(ev){
            ev.preventDefault();
            let obj = {
                method: this.data.method,
                credentials: 'same-origin',
                headers:{
                    'Content-Type': 'application/json'
                }
            }
            if(this.data.method === 'POST' || this.data.method === 'PUT'){
                obj.body = this.data.payload
            }
            if(this.data.authorization !== ''){
                obj.headers.Authorization = `bearer ${this.data.authorization}`
            }
            fetch(apiEndpoint+this.data.endpoint,obj)
                .then(j=> {
                    this.data.response = j.status;
                    this.data.statusClass = j.ok ? 'text-accent' : 'text-warning'
                    return j.json()
                })
                .then(res => {
                    result.innerHTML = prettyPrintJson.toHtml(res)
                })
                .catch(err => {
                    console.log('Error',err)
                })

        }
    }
}