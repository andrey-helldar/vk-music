<template>
    <div class="container">
        <h3>
            {{ trans.title }}
        </h3>

        <form class="row" name="feedback" v-on:submit.prevent="submitForm">

            <div class="input-field col s6">
                <input id="first_name" name="first_name" type="text" class="validate" required v-bind:value="user.first_name">
                <label for="first_name">
                    {{ trans.firstName }}
                </label>
            </div>
            <div class="input-field col s6">
                <input id="last_name" name="last_name" type="text" class="validate" required v-bind:value="user.last_name">
                <label for="last_name">
                    {{ trans.lastName }}
                </label>
            </div>

            <div class="input-field col s12">
                <input id="email" name="email" type="email" class="validate" required v-bind:value="user.email">
                <label for="email">
                    {{ trans.email }}
                </label>
            </div>

            <div class="input-field col s12">
                <textarea id="description" name="description" class="materialize-textarea" minlength="30" length="3000" required></textarea>
                <label for="description">
                    {{ trans.description }}
                </label>
            </div>

            <div class="s12 center-align">
                <button class="btn btn-primary waves-effect waves-light" type="submit">
                    {{ trans.submit }}
                    <i class="material-icons right">send</i>
                </button>
            </div>

        </form>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                url:   'feedback',
                user:  {
                    first_name: '',
                    last_name:  '',
                    email:      ''
                },
                trans: {
                    title:       'Feedback',
                    firstName:   'First Name',
                    lastName:    'Last Name',
                    email:       'Email',
                    description: 'Description',
                    submit:      'Submit'
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

            this.locale();
            this.$parent.hideLoader();
        },
        methods: {
            locale(){
                this.trans.title = this.$root.$refs.app.trans('interface.title.feedback');
                this.trans.firstName = this.$root.$refs.app.trans('interface.form.first_name');
                this.trans.lastName = this.$root.$refs.app.trans('interface.form.last_name');
                this.trans.email = this.$root.$refs.app.trans('interface.form.email');
                this.trans.description = this.$root.$refs.app.trans('interface.form.description');
                this.trans.submit = this.$root.$refs.app.trans('interface.buttons.submit');
            },
            getFeedback(){
                this.$http.get(this.url).then(
                        (response)=> {
                            this.user = response.data.response;
                            this.updateTextFields();
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

                                this.updateTextFields();
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }
                        }, (response)=> {
                            appFunc.info(response.data.error, 'error');
                        }
                );
            },
            /**
             * Обновление текстовых полей после загрузки страницы.
             */
            updateTextFields(){
                $(document).ready(()=> {
                    Materialize.updateTextFields();
                });
            }
        }
    }
</script>
