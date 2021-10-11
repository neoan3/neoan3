const models = JSON.parse(`{{models}}`);
const safeSpace = Boolean(`{{safeSpace}}`);
const isNewestCli = Boolean(`{{newestCli}}`);

function migrate() {
    return {
        start() {
            this.data.started = true;
            this.data.currentModel = this.data.models[this.data.currentModelIndex];
            if(Object.keys(this.data.models[this.data.currentModelIndex].migrate).length < 1){
                this.data.currentModel.migrate = {};
                this.data.currentModel.migrate[this.data.currentModel.name.toLowerCase()] = this.generateBasis();
            }
        },
        async createNewModel(){
          const obj = {dbCredentials:this.data.credentialName, name: this.data.newModel}
          fetch('{{base}}/api.v1/migrate', {
              method: 'PUT',
              headers: {
                  'Content-Type':'application/json;charset=utf-8'
              },
              body: JSON.stringify(obj)
          }).then(async j => {
              if(j.ok){
                  this.data.models = await j.json();
                  this.data.showCreateModal = false;
                  setTimeout(()=>{
                      this.data.currentModelIndex = this.data.models.findIndex(m => m.name.toLowerCase() === obj.name.toLowerCase())
                  },50)

              }else {
                  alert('Cannot write')
              }
          })
        },
        async finish(){
            this.data.currentModel.dbCredentials = this.data.credentialName;
            localStorage.setItem('neoan3MigrationDatabase', this.data.credentialName)
            fetch('{{base}}/api.v1/migrate',{
                method: 'POST',
                headers: {
                    'Content-Type':'application/json;charset=utf-8'
                },
                body: JSON.stringify(this.data.currentModel)
            }).then(async j => {
                if(j.ok){
                    let result = await j.json();
                    if(result.success === 'safe-space'){
                        alert('safe-space found: database updated automatically')
                    } else {
                        alert('Run `neoan3 migrate models up`')
                    }
                } else {
                    alert('Cannot write')
                }
            })

        },
        data: {
            safeSpace: safeSpace,
            isNewestCli:isNewestCli,
            credentialName: localStorage.getItem('neoan3MigrationDatabase') ? localStorage.neoan3MigrationDatabase : 'neoan3_db',
            showSafeSpaceWarning:true,
            showCreateModal:false,
            newModel:'',
            started: false,
            subName: '',
            currentModelIndex:-1,
            models: models,
            currentModel:{},
            newFieldName:'',
            tables: [
                {'name': '', fields: []}
            ],
            outputJson: ''
        },
        addField(table) {
            this.data.currentModel.migrate[table][this.data.newFieldName] = {...this.baseTemplate.new};
            this.data.newFieldName = '';
        },
        removeField(table, field) {
            delete this.data.currentModel.migrate[table][field];
        },
        removeTable(table) {
            delete this.data.currentModel.migrate[table];
        },
        editable(name) {
            const blocked = ['id', 'insert_date', 'delete_date', this.data.tables[0].name + '_id'];
            return !blocked.includes(name);
        },
        addSub() {
            this.data.currentModel.migrate[this.data.currentModel.name.toLowerCase() + '_' + this.data.subName] = this.generateBasis(true)
            this.data.subName = '';
        },
        generateBasis(relation = false) {
            let migrate = {};
            migrate['id'] = this.baseTemplate.id;
            migrate['insert_date'] = this.baseTemplate.insert_date;
            migrate['delete_date'] = this.baseTemplate.delete_date;
            if (relation) {
                migrate[this.data.currentModel.name.toLowerCase() + '_id'] = this.baseTemplate.fk
            }
            return migrate;
        },


        baseTemplate: {
            "id": {
                "type": "binary(16)",
                "key": "primary",
                "nullable": false,
                "default": false,
                "a_i": false
            },
            "new": {
                "type": "varchar(60)",
                "key": false,
                "nullable": true,
                "default": false,
                "a_i": false
            },
            "fk": {
                "type": "binary(16)",
                "key": false,
                "nullable": false,
                "default": false,
                "a_i": false
            },
            "insert_date": {
                "type": "timestamp",
                "key": false,
                "nullable": true,
                "default": "current_timestamp()",
                "a_i": false
            },
            "delete_date": {
                "type": "datetime",
                "key": false,
                "nullable": true,
                "default": false,
                "a_i": false
            }
        }
    }
}