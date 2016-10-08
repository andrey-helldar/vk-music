<template>
    <div v-cloak>
        <div class="container">
            <h3>
                {{ title }}
            </h3>

            <form class="row" name="feedback" v-on:submit.prevent="submitForm">

                <div class="row">
                    <div class="input-field col s6">
                        <input id="first_name" name="first_name" type="text" class="validate" required v-bind:value="user.first_name">
                        <label for="first_name">First Name</label>
                    </div>
                    <div class="input-field col s6">
                        <input id="last_name" name="last_name" type="text" class="validate" required v-bind:value="user.last_name">
                        <label for="last_name">Last Name</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input id="email" name="email" type="email" class="validate" required v-bind:value="user.email">
                        <label for="email">Email</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="description" name="description" class="materialize-textarea" minlength="30" length="3000" required></textarea>
                        <label for="description">Description</label>
                    </div>
                </div>

                <div class="row center-align">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        Submit
                        <i class="material-icons right">send</i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                title: 'Feedback',
                url:   'feedback',
                user:  {
                    first_name: '',
                    last_name:  '',
                    email:      ''
                }
            }
        },
        beforeMount(){
            this.getFeedback();
        },
        mounted(){
            appFunc.console('Component Feedback ready.');

            $(document).ready(()=> {
                $('#description').trigger('autoresize');
                $('#description').characterCounter();

                Materialize.updateTextFields();
            });

            this.$parent.hideLoader();
        },
        methods: {
            getFeedback(){
                this.$http.get(this.url).then(
                        (response)=> {
                            this.user = response.data.response;
                            Materialize.updateTextFields();
                        }
                );
            },
            submitForm(){
                var
                        form        = $('form[name=feedback]'),
                        first_name  = form.find('input[name=first_name]'),
                        last_name   = form.find('input[name=last_name]'),
                        email       = form.find('input[name=email]'),
                        description = form.find('textarea[name=description]');

                this.$http.post(this.url, {
                    first_name:  first_name.val().trim(),
                    last_name:   last_name.val().trim(),
                    email:       email.val().trim(),
                    description: description.val().trim()
                }).then(
                        (response)=> {
                            if (response.data.error == undefined) {
                                appFunc.info(response.data.response, 'success');

                                first_name.val('');
                                last_name.val('');
                                email.val('');
                                description.val('');

                                Materialize.updateTextFields();
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }
                        }, (response)=> {
                            appFunc.info(response.data.error, 'error');
                        }
                );
            }
        }
    }
</script>
