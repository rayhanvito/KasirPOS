<template>
    <div
        class="footer"
        v-if="frontAppSetting && frontAppSetting.footer_company_description"
    >
        <a-row type="flex" justify="center">
            <a-col :span="20">
                <a-row :gutter="[30, 30]">
                    <a-col :xs="24" :sm="24" :md="24" :lg="10" :xl="10">
                        <img :src="frontWarehouse.dark_logo_url" class="footer-logo" />
                        <p class="site-description mt-20">
                            {{ frontAppSetting.footer_company_description }}
                        </p>
                        <div class="mt-10">
                            <a-typography-title :level="5">{{
                                $t("front.follow_us")
                            }}</a-typography-title>
                            <div class="social-icon-list">
                                <a
                                    :href="
                                        frontAppSetting.facebook_url != ''
                                            ? frontAppSetting.facebook_url
                                            : '#'
                                    "
                                    target="_blank"
                                    class="social-icon facebook-color"
                                >
                                    <FacebookFilled />
                                </a>
                                <a
                                    :href="
                                        frontAppSetting.twitter_url != ''
                                            ? frontAppSetting.twitter_url
                                            : '#'
                                    "
                                    target="_blank"
                                    class="social-icon twitter-color ml-5"
                                >
                                    <TwitterCircleFilled />
                                </a>
                                <a
                                    :href="
                                        frontAppSetting.instagram_url != ''
                                            ? frontAppSetting.instagram_url
                                            : '#'
                                    "
                                    target="_blank"
                                    class="social-icon instagram-color ml-5"
                                >
                                    <InstagramFilled />
                                </a>
                                <a
                                    :href="
                                        frontAppSetting.linkedin_url != ''
                                            ? frontAppSetting.linkedin_url
                                            : '#'
                                    "
                                    target="_blank"
                                    class="social-icon linkedin-color ml-5"
                                >
                                    <LinkedinFilled />
                                </a>
                                <a
                                    :href="
                                        frontAppSetting.youtube_url != ''
                                            ? frontAppSetting.youtube_url
                                            : '#'
                                    "
                                    target="_blank"
                                    class="social-icon youtube-color ml-5"
                                >
                                    <YoutubeFilled />
                                </a>
                            </div>
                        </div>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="14" :xl="14">
                        <a-row :gutter="[30, 30]">
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-typography-title :level="5">
                                    {{ $t("front_setting.useful_links") }}
                                </a-typography-title>
                                <ul class="footer-links">
                                    <li
                                        v-for="(
                                            item, index
                                        ) in frontAppSetting.links_widget"
                                        :key="index"
                                    >
                                        <a :href="item.value" target="_blank">
                                            {{ item.title }}
                                        </a>
                                    </li>
                                </ul>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-typography-title :level="5">
                                    {{ $t("front_setting.pages") }}
                                </a-typography-title>
                                <ul class="footer-links">
                                    <li
                                        v-for="(
                                            item, index
                                        ) in frontAppSetting.pages_widget"
                                        :key="index"
                                    >
                                        <a :href="item.value" target="_blank">
                                            {{ item.title }}
                                        </a>
                                    </li>
                                </ul>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-typography-title :level="5">
                                    {{ $t("front_setting.contact") }}
                                </a-typography-title>
                                <div
                                    v-for="(
                                        item, index
                                    ) in frontAppSetting.contact_info_widget"
                                    :key="index"
                                    class="mt-10"
                                >
                                    <strong>{{ item.title }}</strong> :<br />
                                    <span style="font-size: 13px">{{ item.value }}</span>
                                </div>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
    <div
        class="footer-copyright"
        v-if="frontAppSetting && frontAppSetting.footer_company_description"
    >
        <a-row type="flex" justify="center">
            <a-col :span="18">
                <div>{{ frontAppSetting.footer_copyright_text }}</div>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import {
    FacebookFilled,
    TwitterCircleFilled,
    LinkedinFilled,
    InstagramFilled,
    YoutubeFilled,
    ShoppingCartOutlined,
} from "@ant-design/icons-vue";
import cart from "../../../../common/composable/cart";

export default {
    props: ["warehouse"],
    components: {
        ShoppingCartOutlined,
        FacebookFilled,
        LinkedinFilled,
        InstagramFilled,
        YoutubeFilled,
        TwitterCircleFilled,
    },
    setup() {
        const { formatAmountCurrency, frontAppSetting, frontWarehouse } = cart();

        return {
            frontAppSetting,
            formatAmountCurrency,
            frontWarehouse,
        };
    },
};
</script>

<style lang="less">
.footer {
    background: #172337;
    padding-top: 50px;
    padding-bottom: 40px;
    border-top: 1px solid #e5e7eb;
    color: #fff;

    .ant-typography {
        color: #c6c6c6;
    }

    .footer-logo {
        width: 150px;
    }

    .mt-5 {
        margin-top: 5px;
    }

    .follow-us {
        font-weight: 400;
        font-size: 18px;
    }

    .social-icon-list {
        display: flex;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        svg {
            height: 20px;
        }
    }

    .facebook-color {
        background: #2366b8;
    }

    .linkedin-color {
        background: #007cbc;
    }

    .twitter-color {
        background: #00abe4;
    }

    .youtube-color {
        background: #e32212;
    }

    .instagram-color {
        background: linear-gradient(
            to top,
            #ffc107 0%,
            #f44336 31%,
            #9c27b0 65%,
            #9c27b0 100%
        );
    }

    .ml-5 {
        margin-left: 5px;
    }

    .footer-links {
        list-style: none;
        padding-left: 0px;

        li {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        a {
            color: #fff;
        }
    }
}

.footer-copyright {
    background: #172337;
    border-top: 1px solid #454d5e;
    padding-top: 20px;
    padding-bottom: 20px;
    text-align: center;
    color: #c6c6c6;
}
</style>
