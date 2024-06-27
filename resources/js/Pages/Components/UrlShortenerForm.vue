<template>
  <div class="container h-100 ">
      <div class="row justify-content-center align-items-center h-100">
          <div class="col-md-4">
              <form @submit.prevent="submit">
                  <div class="form-group">
                      <label for="original_url">Enter your url</label>
                      <input type="text" class="form-control" id="original_url" placeholder="https://your-url.com" v-model="original_url">
                      <small id="original_url_help" class="form-text text-muted">Please enter the full URL you want to be shortened.</small>
                      <div v-if="errors.original_url" class="alert alert-danger">
                          {{ errors.original_url[0] }}
                      </div>
                      <div v-if="general_errors" class="alert alert-danger">
                          {{ general_errors }}
                      </div>
                      <div v-if="shortened_url" class="alert alert-success">
                          This is your newly generated short url: {{ shortened_url }}
                      </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Shorten URL</button>
              </form>
          </div>
      </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue'

const original_url = ref('');
const shortened_url = ref('');
const general_errors = ref(null);
const errors = ref({});

function submit() {

  shortened_url.value = '';
  general_errors.value = '';
  errors.value = {};       

  axios.post(route('store.url'), { original_url: original_url.value })
      .then((response) => {
        shortened_url.value = response.data.shortened_url
        original_url.value = '';  // Clear the input after successful submission
      })
      .catch((error) => {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {          console.error('Error:', error.response.data.message);

          general_errors.value = error.response.data.message
        }
      });
}
</script>

<style>
  form{
    margin-top: 10rem;
  }
  button{
    width: 100%;
  }
</style>