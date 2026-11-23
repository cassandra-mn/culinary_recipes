<script setup lang="ts">
</script>

<template>
  <main class="container"><RouterView /></main>
</template>

<style>
.container {
  padding: 50px;
}
.d-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
a {
  text-decoration: none;
}
.m-0 {
  margin: 0;
}
.px-1 {
  padding: 0 1%;
}
.text-fff {
  color: #fff;
}
.action {
  border-radius: 50%;
  opacity: 0.8;
  margin: 0;
  background: transparent;
}
.icon {
  width: 22px;
  height: 22px;
}
li {
  border-bottom: #1c2128 1px solid;
  padding: 3px 0;
}
.auth-error {
  color: red;
}
</style>