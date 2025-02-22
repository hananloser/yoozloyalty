<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex xs10 sm7 md5 lg3 xl2>
                <v-form
                    data-vv-scope="form1"
                    :model="form1"
                    lazy-validation
                    @submit.prevent="submitForm('form1')"
                    autocomplete="off"
                    method="post"
                >
                    <v-card class="my-4 elevation-18">
                        <v-toolbar flat color="transparent">
                            <v-toolbar-title>{{
                                $t("login_head")
                            }}</v-toolbar-title>
                        </v-toolbar>
                        <v-card-text>
                            <v-alert
                                :value="app.demo === true"
                                type="info"
                                class="mb-4"
                            >
                                <strong
                                    ><a
                                        @click="
                                            form1.email = 'admin@example.com';
                                            form1.password = 'welcome123';
                                        "
                                        style="color:#fff; text-decoration: underline"
                                        >Demo admin login</a
                                    >:</strong
                                ><br />
                                admin@example.com / welcome123<br /><br />

                                <strong
                                    ><a
                                        @click="
                                            form1.email = 'user@example.com';
                                            form1.password = 'welcome123';
                                        "
                                        style="color:#fff; text-decoration: underline"
                                        >Demo user login</a
                                    >:</strong
                                ><br />
                                user@example.com / welcome123
                            </v-alert>

                            <v-alert
                                :value="form1.has_error"
                                type="error"
                                class="mb-4"
                            >
                                {{ $t("login_not_recognized") }}
                            </v-alert>

                            <v-alert
                                :value="successRegistrationRedirect"
                                type="success"
                                class="mb-4"
                            >
                                {{ $t("successfully_registered_info") }}
                            </v-alert>

                            <v-alert
                                :value="successResetRedirect"
                                type="success"
                                class="mb-4"
                            >
                                {{ $t("reset_email_sent") }}
                            </v-alert>

                            <v-alert
                                :value="successResetUpdateRedirect"
                                type="success"
                                class="mb-4"
                            >
                                {{ $t("password_reset_success") }}
                            </v-alert>

                            <v-text-field
                                type="email"
                                v-model="form1.email"
                                data-vv-name="email"
                                v-validate="'required|email'"
                                :label="$t('email_address')"
                                :error-messages="errors.collect('form1.email')"
                                required
                                prepend-inner-icon="email"
                            ></v-text-field>

                            <v-text-field
                                type="password"
                                v-model="form1.password"
                                data-vv-name="password"
                                v-validate="'required|min:8|max:24'"
                                :label="$t('password')"
                                :error-messages="
                                    errors.collect('form1.password')
                                "
                                required
                                prepend-inner-icon="lock"
                            ></v-text-field>

                            <v-layout align-center justify-end row>
                                <v-btn
                                    text
                                    small
                                    :to="{ name: 'password.email' }"
                                    tabindex="-1"
                                    color="grey"
                                    class="no-caps"
                                    >{{ $t("forgot_password") }}</v-btn
                                >
                            </v-layout>

                            <v-switch
                                name="rememberMe"
                                v-model="form1.rememberMe"
                                :label="$t('remember_me')"
                            ></v-switch>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                :color="$store.getters.app.color_name"
                                large
                                block
                                :loading="form1.loading"
                                :disabled="form1.loading"
                                type="submit"
                                class="ml-0"
                                >{{ $t("login") }}</v-btn
                            >
                        </v-card-actions>
                    </v-card>
                    <v-btn
                        @click="toRegister"
                        :disabled="form1.loading"
                        large
                        block
                        text
                        class="no-caps"
                        >{{ $t("or_create_a_new_account") }}
                        <v-icon size="16" class="ml-1"
                            >arrow_forward</v-icon
                        ></v-btn
                    >
                </v-form>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
export default {
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            successRegistrationRedirect: false,
            successResetRedirect: false,
            successResetUpdateRedirect: false,
            form1: {
                loading: false,
                email: null,
                password: null,
                rememberMe: true,
                has_error: false
            }
        };
    },
    created() {
        this.successRegistrationRedirect =
            this.$route.params.successRegistrationRedirect || false;
        this.form1.email = this.$route.params.email || null;
        this.successResetRedirect =
            this.$route.params.successResetRedirect || false;
        this.successResetUpdateRedirect =
            this.$route.params.successResetUpdateRedirect || false;
    },
    methods: {
        toRegister() {
            this.$router.push({ name: "register" });
        },
        submitForm(formName) {
            this[formName].has_error = false;
            this[formName].loading = true;

            this.$validator.validateAll(formName).then(valid => {
                if (valid) {
                    this.login(formName);
                } else {
                    this[formName].loading = false;
                    return false;
                }
            });
        },
        login(formName) {
            let app = this[formName];

            this.$auth.login({
                rememberMe: app.rememberMe,
                fetchUser: true,
                data: {
                    locale: this.$i18n.locale,
                    email: app.email,
                    password: app.password,
                    remember: app.rememberMe,
                },
                redirect: null,
            })
                .then(() => {
                    let redirectAfterLogin = 'user.dashboard';

                    if (this.$auth.check([1, 2])) {
                        redirectAfterLogin = 'admin.dashboard';
                    }

                    this.$router.push({ name: redirectAfterLogin });
                }, () => {
                    app.has_error = true;

                    app.loading = false;
                });

            // this.$auth.login({
            //     rememberMe: app.rememberMe,
            //     fetchUser: true,
            //     params: {
            //         locale: this.$i18n.locale,
            //         email: app.email,
            //         password: app.password,
            //         remember: app.rememberMe
            //     },
            //     success: function(user) {
            //         let redirectAfterLogin = 'user.dashboard';

            //         if ([1, 2].includes(this.$auth.user().role)) {
            //             redirectAfterLogin = 'admin.dashboard';
            //         }

            //         this.$router.push({ name: redirectAfterLogin });
            //     },
            //     error: function() {
            //         app.has_error = true;
            //         app.loading = false;
            //     }
            // });
        }
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    }
};
</script>
<style scoped></style>
