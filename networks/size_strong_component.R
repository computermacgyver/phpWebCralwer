#Launch R in this directory then run this script, i.e.
# R --vanilla <size_strong_component.R> compontent_sizes.out.txt

library(igraph)

files<-list.files(pattern="\\.graphml$")

for (file in files) {
	print(file)
	
	g<-read.graph(file,"graphml")
	summary(g)
	res<-clusters(g, mode="strong")
	print(max(res$csize))
	print(max(res$csize)/length(V(g)))
	#dist<-cluster.distribution(g, cumulative = FALSE)
}
