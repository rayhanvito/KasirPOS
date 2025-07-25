<template>
    <a-button v-if="isLoggedIn" type="link">
        <router-link
            :to="{ name: 'front.dashboard', params: { warehouse: frontWarehouse.slug } }"
        >
            <a-avatar :src="user.profile_image_url" :size="28" />
        </router-link>
    </a-button>
    <a-button type="link" @click="showLogin" v-else>
        <user-outlined
            :style="{
                fontSize: '24px',
                color: '#fff',
                verticalAlign: 'top',
            }"
        />
    </a-button>

    <a-modal
        v-model:open="visible"
        centered
        :footer="null"
        :title="null"
        :width="450"
        :closable="false"
        :bodyStyle="{ borderRadius: '10px' }"
        wrapClassName="login-register-modal"
        @cancel="loginModalClosed"
    >
        <div v-if="loginForm">
            <div :style="{ textAlign: 'center' }" class="mt-10">
                <a-typography-title :level="2" :style="{ marginBottom: '0px' }">
                    {{ $t("front.login") }}
                </a-typography-title>
                <p :style="{ color: '#6b7280', fontSize: '16px' }">
                    {{ $t("front.login_using_email_password") }}
                </p>
            </div>

            <a-form layout="vertical" class="mt-30">
                <a-alert
                    v-if="errorMessage != ''"
                    :message="errorMessage"
                    type="error"
                    class="mb-10"
                    show-icon
                />

                <a-form-item
                    :label="$t('user.email_phone')"
                    name="email"
                    :help="rules.email ? rules.email.message : null"
                    :validateStatus="rules.email ? 'error' : null"
                >
                    <a-input
                        v-model:value="credentials.email"
                        @pressEnter="onLogin"
                        :placeholder="
                            $t('common.placeholder_default_text', [
                                $t('user.email_phone'),
                            ])
                        "
                    >
                        <template #prefix>
                            <mail-outlined />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item
                    :label="$t('user.password')"
                    name="password"
                    :help="rules.password ? rules.password.message : null"
                    :validateStatus="rules.password ? 'error' : null"
                >
                    <a-input-password
                        v-model:value="credentials.password"
                        @pressEnter="onLogin"
                        :placeholder="
                            $t('common.placeholder_default_text', [$t('user.password')])
                        "
                        autocomplete="off"
                    >
                        <template #prefix>
                            <lock-outlined />
                        </template>
                    </a-input-password>
                </a-form-item>

                <a-form-item>
                    <a-button
                        type="primary"
                        class="mt-10"
                        @click="onLogin"
                        :loading="loading"
                        block
                    >
                        {{ $t("front.login") }}
                    </a-button>
                </a-form-item>

                <a-form-item>
                    <a-typography-text strong>
                        {{ $t("front.dont_have_account") + " " }}
                    </a-typography-text>
                    <a-button @click="loginForm = false" type="link" class="p-0">
                        {{ $t("front.signup") }}
                    </a-button>
                </a-form-item>
            </a-form>
        </div>

        <div v-else>
            <div :style="{ textAlign: 'center' }" class="mt-10">
                <a-typography-title :level="2" :style="{ marginBottom: '0px' }">
                    {{ $t("front.signup") }}
                </a-typography-title>
                <p :style="{ color: '#6b7280', fontSize: '16px' }">
                    {{ $t("front.signup_using_email_password") }}
                </p>
            </div>

            <a-alert v-if="signupSuccess" type="success" class="mt-10" show-icon>
                <template #message>{{ $t("common.success") }}</template>
                <template #description>
                    {{ $t("front.register_successfully") }}
                    <a-button @click="loginForm = true" type="link" class="p-0">
                        {{ $t("front.click_here_to_login") }}
                    </a-button>
                </template>
            </a-alert>

            <a-form v-else layout="vertical" class="mt-30">
                <a-form-item
                    :label="$t('user.name')"
                    name="name"
                    :help="rules.name ? rules.name.message : null"
                    :validateStatus="rules.name ? 'error' : null"
                >
                    <a-input
                        v-model:value="signupCredentials.name"
                        @pressEnter="onSignup"
                        :placeholder="
                            $t('common.placeholder_default_text', [$t('user.name')])
                        "
                    >
                        <template #prefix>
                            <UserOutlined />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item
                    :label="$t('user.email')"
                    name="email"
                    :help="rules.email ? rules.email.message : null"
                    :validateStatus="rules.email ? 'error' : null"
                >
                    <a-input
                        v-model:value="signupCredentials.email"
                        @pressEnter="onSignup"
                        :placeholder="
                            $t('common.placeholder_default_text', [$t('user.email')])
                        "
                    >
                        <template #prefix>
                            <MailOutlined />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item
                    :label="$t('user.phone')"
                    name="phone"
                    :help="rules.phone ? rules.phone.message : null"
                    :validateStatus="rules.phone ? 'error' : null"
                >
                    <a-input
                        v-model:value="signupCredentials.phone"
                        @pressEnter="onSignup"
                        :placeholder="
                            $t('common.placeholder_default_text', [$t('user.phone')])
                        "
                    >
                        <template #prefix>
                            <PhoneOutlined />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item
                    :label="$t('user.password')"
                    name="password"
                    :help="rules.password ? rules.password.message : null"
                    :validateStatus="rules.password ? 'error' : null"
                >
                    <a-input-password
                        v-model:value="signupCredentials.password"
                        @pressEnter="onSignup"
                        :placeholder="
                            $t('common.placeholder_default_text', [$t('user.password')])
                        "
                        autocomplete="off"
                    >
                        <template #prefix>
                            <lock-outlined />
                        </template>
                    </a-input-password>
                </a-form-item>

                <a-form-item>
                    <a-button
                        type="primary"
                        class="mt-10"
                        @click="onSignup"
                        :loading="loading"
                        block
                    >
                        {{ $t("front.signup") }}
                    </a-button>
                </a-form-item>

                <a-form-item>
                    <a-typography-text strong>
                        {{ $t("front.already_have_account") + " " }}
                    </a-typography-text>
                    <a-button @click="loginForm = true" type="link" class="p-0">
                        {{ $t("front.login") }}
                    </a-button>
                </a-form-item>
            </a-form>
        </div>
    </a-modal>
</template>

<script>
import { defineComponent, ref, computed, watch } from "vue";
import {
    UserOutlined,
    MailOutlined,
    LockOutlined,
    PhoneOutlined,
} from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import apiFront from "../../../../common/composable/apiFront";

export default defineComponent({
    props: ["modalVisible"],
    emits: ["modalClosed"],
    components: {
        UserOutlined,
        MailOutlined,
        LockOutlined,
        PhoneOutlined,
    },
    setup(props, { emit }) {
        const store = useStore();
        const credentials = ref({
            email: null,
            password: null,
        });
        const signupCredentials = ref({
            name: null,
            phone: null,
            email: null,
            password: null,
        });
        const visible = ref(false);
        const { t } = useI18n();
        const router = useRouter();
        const loginForm = ref(true);
        const signupSuccess = ref(false);

        const { addEditRequestFront, loading, rules } = apiFront();
        const { frontWarehouse } = common();
        const errorMessage = ref("");

        const showLogin = () => {
            visible.value = true;
        };

        const onLogin = () => {
            errorMessage.value = "";

            addEditRequestFront({
                url: "front/login",
                data: credentials.value,
                successMessage: t("front.logged_in_successfully"),
                success: (res) => {
                    store.commit("front/updateUser", res.user);
                    store.commit("front/updateToken", res.token);
                    store.commit("front/updateExpires", res.expires_in);

                    visible.value = false;

                    router.push({
                        name: "front.dashboard",
                        params: { warehouse: frontWarehouse.value.slug },
                    });
                },
                error: (errorRules) => {
                    if (errorRules.error_message) {
                        errorMessage.value = errorRules.error_message;
                    }
                },
            });
        };

        const onSignup = () => {
            errorMessage.value = "";

            addEditRequestFront({
                url: "front/signup",
                data: signupCredentials.value,
                successMessage: t("front.register_successfully"),
                success: (res) => {
                    signupSuccess.value = true;
                },
                error: (errorRules) => {
                    if (errorRules.error_message) {
                        errorMessage.value = errorRules.error_message;
                    }
                },
            });
        };

        const loginModalClosed = () => {
            emit("modalClosed");
            loginForm.value = true;
        };

        watch(props, (newVal, oldVal) => {
            visible.value = newVal.modalVisible;
        });

        return {
            credentials,
            rules,
            visible,
            showLogin,
            onLogin,
            errorMessage,
            loading,
            frontWarehouse,

            user: computed(() => store.state.front.user),
            isLoggedIn: computed(() => store.getters["front/isLoggedIn"]),
            loginModalClosed,

            loginForm,
            signupCredentials,
            onSignup,
            signupSuccess,
        };
    },
});
</script>

<style lang="less">
.login-register-modal .ant-modal-content {
    border-radius: 10px;
}
</style>
