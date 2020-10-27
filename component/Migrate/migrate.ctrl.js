const models = JSON.parse('{{models}}');
function migrate() {
    return {
        start() {
            this.data.started = true;
            this.data.currentModel = models[this.data.currentModelIndex];
            if(Object.keys(models[this.data.currentModelIndex].migrate).length < 1){
                this.data.currentModel.migrate = {};
                this.data.currentModel.migrate[this.data.currentModel.name.toLowerCase()] = this.generateBasis();
            }
        },
        async finish(){
            fetch('{{base}}/api.v1/migrate',{
                method: 'POST',
                headers: {
                    'Content-Type':'application/json;charset=utf-8'
                },
                body: JSON.stringify(this.data.currentModel)
            }).then(async j => {
                if(j.ok){
                    let result = await j.json();
                    console.log(result)
                    alert('Run `neoan3 migrate models up`')
                } else {
                    alert('Cannot write')
                }
            })

        },
        data: {
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