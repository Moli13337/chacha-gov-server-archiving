<template>
	<FooterNote
		:title="title"
		:content="content"
	/>
</template>
<script>


import FooterNote from '~/components/footernote';
export default {
	components: {
		FooterNote
	},
	data() {
		return {
			title: '关于我们',
			content: `	<section>
            待完善...
					</section>`
		};
	},

};
</script>
<style lang="less" >

</style>

