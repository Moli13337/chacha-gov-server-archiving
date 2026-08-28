<template>
	<div>
		<empty
			:hasBtn="false"
			v-if="list && list.length == 0"
		/>
		<div
			class="rate-container"
			v-for="(item, index) in list"
			:key="index"
			v-else
		>
			<div class="avatar">
				<el-avatar
					:size="size"
					src="HeadPortrait"
				></el-avatar>
			</div>
			<div
				class="rate-box"
			>
				<p>{{item.user_name}}</p>
				<div class="rate">
					<el-rate
						v-model="item.stars"
						disabled
						text-color="#ff9900"
						score-template="{value}"
					>
					</el-rate>
					<p class="time">{{item.created_at | formatDate(format="YYYY/MM/DD HH:mm:ss")}}</p>
				</div>
				<div class="text">
					{{item.content}}
				</div>
				<el-divider></el-divider>
			</div>
		</div>
	</div>
</template>
<script>
import HeadPortrait from '@/assets/images/head-portrait.png';
import empty from '@/components/empty';
export default {
	props: {
		list: {
			type: Array,
			default: function () {
				return [];
			}
		}
	},
	components: {
		empty,
	},
	data() {
		return {
			HeadPortrait: HeadPortrait,
			size: 60
		};
	}
};
</script>
<style lang="less" scoped>
.rate-container {
  display: flex;
   .avatar {
    width: 80px;
    text-align: center;
    padding-top: 20px;
  }
  .rate-box {
    flex: 1;
    .time {
      color: #B5B5B5;
    }
    .rate {
      display: flex;
      justify-content: space-between;
      padding-bottom: 10px;
    }
    .text {
      color:rgba(129,129,129,1);
      font-size:16px;
    }
  }
}
  .rate-empty-box{
    width: 100%;
    display: flex;
    padding-top: 100px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    .empty {

      text-align: center;
    }
    p {
      padding: 20px;
    }
  }
</style>
