import dashboard from "./dashboard";
import settings from "./settings";
import company from "./company";
import paymentTranscation from "./paymentTranscation";
import users from "./users";
import subscriptionPlan from "./subscriptionPlan";
import websiteSettings from "./websiteSettings";
import setupAppSuperadmin from "./setupApp";
import emailQueries from "./emailQueries";

export default [
    ...dashboard,
    ...settings,
    ...company,
    ...paymentTranscation,
    ...users,
    ...subscriptionPlan,
    ...websiteSettings,
    ...setupAppSuperadmin,
    ...emailQueries,
];
