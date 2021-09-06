gritter = {
    show: function(message, status, errors) {
        var arg = "";
        if (errors != undefined) {
            $.each(errors,function (key,error) {
                arg += "<br/>";
                if (error.variables != null && error.variables.property != null) {
                    arg += error.variables.property + ': ';
                }
                arg += error.message;
            });
        }

        if(arg != "") {
            message += "<br/>" + arg;
        }

        var class_name = "gritter-danger";
        if (status) {
            class_name = "gritter-primary";
        }
        $.gritter.add({
            text: message,
            class_name: class_name
        });
    }
}
