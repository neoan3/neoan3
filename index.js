var app = require('express')();
var http = require('http').Server(app);
// database

var mysqlConnection;

var mysql = require('mysql');
var pool = mysql.createPool({
    "connectionLimit":20,
    "host":"localhost",
    "user":"root",
    "password":"",
    "database":"eagle"
});




var calls;
calls = {
    auth: function (socket,data) {
        pool.getConnection(function(err,connection){
            connection.query(
                'SELECT user.id FROM user JOIN user_handshake as h ON h.user_id = user.id WHERE h.handshake="'+data.neoan_handshake+'"',
                function (error, results, fields) {

                    if (results.length > 0) {
                        connection.query('UPDATE user SET last_seen = NOW(), socket_id = "'+socket.id+'" WHERE id = '+ results[0].id,function(){
                            connection.release();
                        });
                    } else if(results.length<1){
                        connection.release();
                    }
                    if (error) {
                        throw new Error('QUERY error');
                    }
                    return true;
                });
        })

    },
    findUser:function(data,next){
        if(typeof data.to === 'undefined'){
            next();
        }
        pool.getConnection(function(err,connection){
            connection.query('SELECT socket_id FROM user WHERE id = ?',[data.to],function(error,results){
                if (error) throw error;
                if (results.affectedRows <1) {
                    next(new Error('user not found'));
                }
                console.log(results);
                data.to_socket = results[0].socket_id;
                connection.release();
                next();
            });
        })

    }

};



var io = require('socket.io')(http);


app.get('/', function(req, res){
    res.send('<h1>NeoanPHP Socket running</h1>');
});


io.on('connection', function(socket){

    console.log('anonymous user connected: '+socket.id);
    socket.on('login', function(data) {
        calls.auth(socket,data);
        io.emit('online',{socket_id:socket.id,action:'joined',user_id:data.user_id});
        console.log(socket.id+' identified as user '+data.user_id);
    });
    socket.on('disconnect', function() {
        io.emit('online',{socket_id:socket.id,action:'left'});
    });
    // to certain user-ID
    socket.on('whisper',function(data){
        calls.auth(socket,data);
        console.log('whispering to '+data.to+'...');
        console.log(data.data);

        socket.broadcast.to(data.to).emit('whisper',data.data);

    });
    // public shouting
    socket.on('public',function (msg) {
        console.log(msg);
        io.emit('public',msg);
    });

});

http.listen(3000, function(){
    console.log('listening on *:3000');
});