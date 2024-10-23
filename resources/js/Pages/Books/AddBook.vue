<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "vue-toastification";
const props = defineProps(["errors"]);

const toast = useToast();

const formData = ref({
  name: "",
  author: "",
  cover: null,
});

const handleFileChange = (event) => {
  formData.value.cover = event.target.files[0]; 
};

const submitForm = async () => {
  try {
    const payload = new FormData();
    payload.append("name", formData.value.name);
    payload.append("author", formData.value.author);


    if (formData.value.cover) {
      payload.append("cover", formData.value.cover);
    }
    await axios.post("/books", payload, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    formData.value.name = "";
    formData.value.author = "";
    formData.value.cover = null;
    toast.success("Book Added Successfully");
  } catch (error) {
    if (error.response && error.response.status === 422) {
   
      const messages = error.response.data.errors;
      for (const key in messages) {
        messages[key].forEach((msg) => {
          toast.error(msg); 
        });
      }
    } else {
      toast.error("Failed to Add Book"); 
    }
  }
};
</script>

<style lang="scss" scoped></style>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Add Books</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg  p-4">


          <div class="bg-gray-100 p-4 rounded-sm shadow-sm w-1/2 mx-auto space-y-2">
            <InputLabel for="name" value="Book Title" />
            <TextInput
              id="name"
              type="text"
              class="mt-1 block w-full"
              v-model="formData.name"
              required
              autofocus
            />
            <InputError class="mt-2" :message="errors.name" />

            <InputLabel for="author" value="Author Name" />
            <TextInput
              id="author"
              type="text"
              class="mt-1 block w-full"
              v-model="formData.author"
              required
              autofocus
            />
            <!-- <InputError class="mt-2" :message="formData.errors.author" /> -->

            <InputLabel for="cover" value="Book cover" />
            <input
              id="cover"
              type="file"
              class="mt-1 block w-full"
              @change="handleFileChange"
              required
              accept=".jpeg,.png,.jpg,.gif"
            />
            <!-- <InputError class="mt-2" :message="formData.errors.cover" /> -->

            <PrimaryButton class="mx-auto" @click="submitForm"> SUBMIT </PrimaryButton>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
