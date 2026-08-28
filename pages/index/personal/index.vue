<template>
	<div class="person-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/personal">个人中心</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div v-if="this.$route.path == '/personal/mine' ">我的主页</div>
					<div v-else-if="this.$route.path == '/personal/record' ">我的申报</div>
					<div v-else>消息通知</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div>
			<el-row :gutter="20">
				<el-col :span="5"><div
					class="tips"
					v-if="this.$route.path != '/personal/record/info'"
				>
					<ul class="tip-container">
						<li class="tip-item item-first"><p>个人中心</p><img
							src="~assets/images/icon-person-touxiang.png"
							class="icon-person"
						></li>
						<li
							class="tip-item item-second"
							@click="handleToMin"
							:class="{ 'item-active': this.$route.path == '/personal/mine'}"
						>我的主页</li>
						<li
							class="tip-item item-third"
							@click="handleToMessage"
							:class="{ 'item-active': this.$route.path == '/personal' }"
						>
							<div
								v-if="unreadCount == 0"
								class="item"
							>
								消息通知
							</div>
							<div
								v-else
								class="item"
							>
								<el-badge
									:value="unreadCount"
									class="item"
								>
									消息通知
								</el-badge>
							</div>
						</li>
						<li
							class="tip-item item-five"
							@click="handleCollection"
							:class="{ 'item-active': this.$route.path == '/personal/collection'}"
						>我的收藏</li>
						<li
							class="tip-item item-five"
							@click="handleToRecord"
							:class="{ 'item-active': this.$route.path == '/personal/record'}"
						>我的申报</li>
						<li
							class="tip-item item-five"
							@click="handleRevisedRecord"
							:class="{ 'item-active': this.$route.path == '/personal/revised_record'}"
						>申报资料订正记录</li>
					</ul>
				</div>
				</el-col>
				<nuxt-child
					:value="unreadCount"
				></nuxt-child>
			</el-row>
		</div>
	</div>
</template>
<script>
import {
	FETCH_UNREAD_MESSAGE_COUNT,
	FETCH_USER_INFO
} from '@/utils/urls';
import storage from '@/utils/storage';
export default {
	data() {
		return {
			isActive: false,
		};
	},
	async asyncData({$axios}) {
		return $axios.get(FETCH_UNREAD_MESSAGE_COUNT)
			.then(unreadCount => ({unreadCount}))
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		handleToMin() {
			this.$router.push('/personal/mine');
		},
		handleToMessage() {
			this.$router.push('/personal');
		},
		handleToRecord() {
			this.$router.push('/personal/record');
		},
		handleCollection() {
			this.$router.push('/personal/collection');
		},
		handleRevisedRecord() {
			this.$router.push('/personal/revised_record');
		}
	},
	mounted() {
		this.$axios.get(FETCH_USER_INFO)
			.then(result => {
				const data = result || {};

				storage.setItem('user_info', data);
			})
			.catch(error => {
				console.log(error);
			});
		this.$bus.on('onUnreadCountChange', (unreadCount) => {
			this.unreadCount = unreadCount;
		});
	},
	watch: {
		$route() {
			this.$axios.get(FETCH_UNREAD_MESSAGE_COUNT)
				.then(unreadCount => {
					this.unreadCount = unreadCount;
					this.$bus.emit('onUnreadCountChange', unreadCount);
				})
				.catch(error => {
					console.log(error);
				});
		}
	}
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
.person-container {
  width: 100%;
  .tip-container {
    background: url('~assets/images/bg-person-tip.png');
    background-size: 100% 100%;

  }
  .tip-item {
    width: 100%;
    padding: 30px 30px 30px 35px;
    font-size:16px;
    font-family:Microsoft YaHei;
    font-weight:400;
    color: @backGroundColor;
    cursor: pointer;
  }
  .item-active {
    background:#086EBF;
  }
  .item-first {
    font-size:20px;
    font-family:Microsoft YaHei;
    font-weight:bold;
    color: @backGroundColor;
    display: flex;
    justify-content: flex-start;
    .icon-person {
      width: 25px;
      height: 25px;
      margin-left: 30px;
    }
  }

  .pagination {
    text-align: center;
    padding: 20px 0;
    .number {
      width: 40px;
      height: 40px;
      line-height: 40px;
    }
    .btn-prev, .btn-next {
      height: 40px;
      border:1px solid rgba(235,235,235,1);
      background: none;
      padding: 0 10px;
    }
    .more {
      height: 40px;
      line-height: 40px;
    }
  }
}
</style>

