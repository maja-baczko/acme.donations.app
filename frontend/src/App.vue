<script setup lang="ts">
import Header from "@/layout/Header.vue";
import Main from "@/layout/Main.vue";
import Footer from "@/layout/Footer.vue";
import LoginView from "@/modules/user/views/LoginView.vue";
</script>

<template v-if="!userIsLoggedIn">
    <LoginView />
</template>

<template v-else>
    <Header />
    <Main />
    <Footer />
</template>

<style scoped></style>
